<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends ApiController
{
    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return JsonResponse
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->transformJsonBody($request);
        $username = $request->get('username');
        $password = $request->get('password');
        $email = $request->get('email');

        if (empty($username) || empty($password) || empty($email)){
            return $this->respondValidationError("Invalid Username or Password or Email");
        }
        $user = new User();
        $user->setPassword($encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setRoles(["ROLE_USER"]);
        $em->persist($user);
        $em->flush();
        $response = new JsonResponse(sprintf('User %s successfully created', $user->getUsername()));
        return $response;
        //return $this->respondWithSuccess(sprintf('User %s successfully created', $user->getUsername()));
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTTokenManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user,JWTTokenManagerInterface $JWTTokenManager){
        $response = new JsonResponse(['token'=>$JWTTokenManager->create($user)

        ]);
       // $response->headers->set('Access-Control-Allow-Origin', '*');
        return  $response;
    }
}
