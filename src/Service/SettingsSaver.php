<?php


namespace App\Service;


use App\Entity\User;
use App\Repository\UserRepository;
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

    public function __construct($userRepo, $usuario, $request, $passwordEncoder)
    {
        $this->userRepo = $userRepo;
        $this->usuario = $usuario;
        $this->request = $request;
        $this->passwordEncoder = $passwordEncoder;
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
                dump($valido);
            case 'foto':
                return $this->request->files->get('foto') !== null
                    && $this->request->files->get('foto') instanceof UploadedFile;
        }

        if ($valido && $setting !== 'password') {
            $valido = empty($this->userRepo->findBy([$setting => $this->request->get($setting)]));
        }

        return $valido;
    }

    public function actualizaFoto(SluggerInterface $slugger, $fotoPerfil)
    {
        /** @var UploadedFile $foto */
        $foto = $this->request->files->get('foto');

        // this condition is needed because the 'brochure' field is not required
        // so the PDF file must be processed only when a file is uploaded
        if ($foto) {
            $originalFilename = pathinfo($foto->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $foto->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $foto->move($fotoPerfil, $newFilename);
                $this->usuario->setFoto($newFilename);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
        }
    }
}