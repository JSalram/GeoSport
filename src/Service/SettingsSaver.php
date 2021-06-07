<?php


namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class SettingsSaver
{
    /**
     * @var UserRepository
     */
    private $userRepo;

    /**
     * @var User
     */
    private $usuario;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var bool
     */
    public $cambios;

    public function __construct($userRepo, $usuario, $request, $passwordEncoder)
    {
        $this->userRepo = $userRepo;
        $this->usuario = $usuario;
        $this->request = $request;
        $this->passwordEncoder = $passwordEncoder;
        $this->cambios = false;
    }

    /**
     * @param string $setting
     * @return bool
     */
    public function validar(string $setting): bool
    {
        $valido = true;

        switch ($setting) {
            case 'username':
                $valido = $this->usuario->getUsername() !== $this->request->get($setting);
                break;
            case 'email':
                $valido = $this->usuario->getEmail() !== $this->request->get($setting);
                break;
            case 'password':
                $valido = $this->passwordEncoder->isPasswordValid($this->usuario, $this->request->get('oldPassword'))
                    && $this->request->get('newPassword') === $this->request->get('repeatPassword');
                break;
            case 'foto':
                $filename = $this->request->files->get('foto');
                $valido = $filename !== null && $filename instanceof UploadedFile;
        }

        if ($valido && $setting !== 'password' && $setting !== 'foto') {
            $valido = empty($this->userRepo->findBy([$setting => $this->request->get($setting)]));
        }
        if (!$this->cambios) {
            $this->cambios = $valido;
        }

        return $valido;
    }

    public function actualizaFoto(SluggerInterface $slugger, $fotoPerfil)
    {
        /** @var UploadedFile $foto */
        $foto = $this->request->files->get('foto');

        if ($foto) {
            $originalFilename = pathinfo($foto->getClientOriginalName(), PATHINFO_FILENAME);
            if (in_array($foto->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $foto->guessExtension();

                try {
                    if ($this->usuario->hasFoto()) {
                        $filesystem = new Filesystem();
                        $filesystem->remove($this->usuario->getFoto());
                    }
                    $foto->move($fotoPerfil, $newFilename);
                    $this->usuario->setFoto($newFilename);
                } catch (FileException $e) {
                }
            } else {
                $this->cambios = false;
            }
        }
    }
}