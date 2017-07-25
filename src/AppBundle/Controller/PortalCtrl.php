<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User as User;
use AppBundle\Entity\Institucion as Institucion;
use AppBundle\Entity\Reserva as Reserva;
use AppBundle\Entity\Fecha as Fecha;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ReCaptcha\ReCaptcha;

class PortalCtrl extends Controller
{
    /**
     * @Route("/", name="showIndex")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('PortalCtrl/index.html.twig', [
        //    'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/login", name="showLogin")
     */
    public function showLoginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('AdminCtrl/showLogin.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/login/check", name="runLogin")
     *  @Method("POST")
     */
    public function runLoginAction(Request $request)
    {
    }

    /**
         * @Route("/login/redirect", name="runLoginRedirect")
         */
        public function runLoginRedirectAction(Request $request)
        {
            if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                throw $this->createAccessDeniedException();
            }
            if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('showDashboard');
            } else {
                return $this->redirectToRoute('showIndex');
            }
        }

    /**
     * @Route("/formulario", name="showReserva")
     *  @Method("GET")
     */
    public function showReservaAction()
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $query = $qb->select('fecha')->from('AppBundle:Fecha', 'fecha')->where('fecha.cuposRestantes > 0')
            ->orderBy('fecha.metafecha', 'ASC')->getQuery();
        $fechas = $query->getArrayResult();
        return $this->render('PortalCtrl/showReserva.html.twig', [
            'fechas' => $fechas
        ]);
    }

    /**
     * @Route("/reserva/run", name="runReserva")
     *  @Method("POST")
     */
    public function runReservaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $request->request;
        $recaptcha = new ReCaptcha('6LeInigUAAAAAO55O404K5tJMBNwhw3jkLKVBTn3');
        // Verify with the g-recaptcha-response.
        $resp = $recaptcha->verify($post->get('g-recaptcha-response'), $request->getClientIp());
        if (!$resp->isSuccess()) {
            // Do something if the submit wasn't valid !
            // Use the message to show something
            $this->addFlash(
                'error',
                'El validador Google reCAPTCHA "No soy un robot" falló o caducó'
            );
            return $this->redirectToRoute('showReserva');
        }

        $tokenValue = $post->get('csrf_token');
        //        $token = new CsrfToken($tokenId, $tokenValue);
        if (!$this->isCsrfTokenValid('formReservar', $tokenValue)) {
            $this->addFlash(
                'error',
                'Token CSRF invalido'
            );
            return $this->redirectToRoute('showReserva');
        }
        // GOOOOOO
        $repo = $em->getRepository('AppBundle:Fecha');
        $fecha = $repo->findOneById($post->get('reserva-fechaID'));
        if($fecha->getCuposRestantes() < ($post->get('reserva-cupo'))){
            $this->addFlash(
                'error',
                'La fecha seleccionada cuenta con ' . $fecha->getCuposRestantes() . ' lugares restantes y usted ingresó que vendran ' . $post->get('reserva-cupo') . ' alumnos. Por favor elija una fecha donde haya cantidad suficiente para el total de alumnos que visitarán.'
            );
            return $this->redirectToRoute('showReserva');
        }

        $reserva = new Reserva();

        $reserva->setCupo($post->get('reserva-cupo'));
        $reserva->setFecha($fecha);

        $institucion = new Institucion();

        $institucion->setNombre($post->get('institucion-nombre'));
        $institucion->setDireccion($post->get('institucion-direccion'));
        $institucion->setEmail($post->get('institucion-email'));
        $institucion->setLocalidad($post->get('institucion-localidad'));
        $institucion->setProvincia($post->get('institucion-provincia'));
        $institucion->setTelefono($post->get('institucion-telefono'));
        $institucion->setTerminalidad($post->get('institucion-terminalidades'));
        $institucion->setInfoExtra(($post->get('institucion-extra') ? : null));
        $institucion->setDocenteNombre($post->get('docente-nombre'));
        $institucion->setDocenteEmail($post->get('docente-email'));
        $institucion->setDocenteTelefono($post->get('docente-telefono'));
        $institucion->setOptativoNombre(($post->get('aux-nombre') ? : null));
        $institucion->setOptativoEmail(($post->get('aux-email') ? : null));
        $institucion->setOptativoTelefono(($post->get('aux-telefono') ? : null));

        $institucion->setReserva($reserva);

        $restantes = $fecha->getCuposRestantes() - ($post->get('reserva-cupo'));
        $fecha->setCuposRestantes($restantes);

        $em->persist($reserva);
        $em->persist($institucion);
        $em->flush();

        $message = \Swift_Message::newInstance()
            ->setSubject('¡Su reserva ha sido registrada!')
            ->setFrom(array('ingreso@frsf.utn.edu.ar' => 'UTN Abierta'))
            ->setTo(array($institucion->getDocenteEmail(),$institucion->getEmail()))
            ->setBody(
            $this->renderView(
                'email/nuevaReserva.html.twig',
                array('reserva' => $reserva, 'institucion' => $institucion )
            ),
            'text/html'
        );

        $this->get('mailer')->send($message);

        return $this->render('Utils/exito.html.twig', array(
            'mensaje' => '¡Se ha enviado un email a su correo electrónico confirmando la reserva! Por favor, compruebe su casilla de mensajes, si no encuentra el email, busque en su carpeta de correo no deseado.',
            'callback' => $this->generateUrl('showIndex')
        ));
    }

    /**
     * @Route("/testing", name="showTest")
     */
    public function shotTestAction()
    {
      return $this->render('email/nuevoAdmin.html.twig', [
          'nombre' => 'Guillermo',
          'apellido' => 'Croppi'
      ]);
    }

    /**
     * @Route("/login/reestablecer", name="showSolicitarReestablecerPassword")
     */
    public function showSolicitarReestablecerPasswordAction(Request $request)
    {

        return $this->render('AdminCtrl/solicitarReestablecer.html.twig', array(

        ));
    }

    /**
     * @Route("/login/reestablecer/run", name="runSolicitarReestablecerPassword")
     *  @Method("POST")
     */
    public function runSolicitarReestablecerPasswordAction(Request $request)
    {
        $post = $request->request;
        $recaptcha = new ReCaptcha('6LeInigUAAAAAO55O404K5tJMBNwhw3jkLKVBTn3');
        // Verify with the g-recaptcha-response.
        $resp = $recaptcha->verify($post->get('g-recaptcha-response'), $request->getClientIp());
        if (!$resp->isSuccess()) {
            // Do something if the submit wasn't valid !
            // Use the message to show something
            $this->addFlash(
                'error',
                'El validador Google reCAPTCHA "No soy un robot" falló o caducó'
            );
            return $this->redirectToRoute('showSolicitarReestablecerPassword');
        }

        $tokenValue = $post->get('csrf_token');
        //        $token = new CsrfToken($tokenId, $tokenValue);
        if (!$this->isCsrfTokenValid('formRecuperar', $tokenValue)) {
            $this->addFlash(
                'error',
                'Token CSRF invalido'
            );
            return $this->redirectToRoute('showSolicitarReestablecerPassword');
        }
        $em = $this->getDoctrine()->getManager();
        $repositoryUsers = $em->getRepository('AppBundle:User');
        $user = $repositoryUsers->findOneByEmail($post->get('user-email'));
        if(!$user){
            return $this->render('Utils/error.html.twig', array(
                'mensaje' => 'No existe ningun usuario con tal email',
                'callback' => $this->generateUrl('showIndex')
            ));
        }
        //Save token
        $emailToken = bin2hex(openssl_random_pseudo_bytes(16));
        $user->setToken($emailToken);
        $user->setIsActive(false);
        $em->flush();
        $urlReestaurar = $this->generateUrl(
            'showReestablecerPassword',
            array('userID' => $user->getID(), 'token' => $emailToken), UrlGeneratorInterface::ABSOLUTE_URL
        );
        $messageUsuario = \Swift_Message::newInstance()
            ->setSubject('Reestablecer contraseña')
            ->setFrom(array('no-reply@utnabierta.com' => 'UTN Abierta'))
            ->setTo($user->getEmail())
            ->setBody(
            $this->renderView(
                'email/reestablecerPassword.html.twig',
                array('link' => $urlReestaurar)
            ),
            'text/html'
        );
        $this->get('mailer')->send($messageUsuario);
        return $this->render('Utils/exito.html.twig', array(
            'mensaje' => 'Se ha enviado un email a su corre electronico para seguir los pasos de su recuperación. Revise su casilla de correo y siga las instrucciones para poder recuperar su cuenta y reestablecerla con una nueva contraseña.',
            'callback' => $this->generateUrl('showIndex')
        ));
    }


    /**
     * @Route("/reestablecer/{userID}/{token}", name="showReestablecerPassword", requirements={"userID": "\d+"})
     */
    public function showReestablecerPasswordAction($userID = 0, $token = '')
    {
        $em = $this->getDoctrine()->getManager();
        $repositoryUsers = $em->getRepository('AppBundle:User');
        $user = $repositoryUsers->findOneById($userID);
        if(!$user){
            return $this->render('Utils/error.html.twig', array(
                'mensaje' => 'No existe el usuario',
                'callback' => $this->generateUrl('showIndex')
            ));
        }
        if($user->getToken() != $token){
            return $this->render('Utils/error.html.twig', array(
                'mensaje' => 'El token es invalido, no esta habilitado para reestablecer su contraseña',
                'callback' => $this->generateUrl('showIndex')
            ));
        }
        return $this->render('AdminCtrl/reestablecerPassword.html.twig', array(
            'userID' => $userID,
            'token' => $token
        ));
    }

    /**
     * @Route("/reestablecer/{userID}/{token}/run", name="runReestablecerPassword", requirements={"userID": "\d+"})
	 * @Method("POST")
     */
    public function runRestablecerPasswordAction($userID = 0, $token = '', Request $request)
    {
        $post = $request->request;
        $tokenValue = $post->get('csrf_token');
        //        $token = new CsrfToken($tokenId, $tokenValue);
        if (!$this->isCsrfTokenValid('formReestablecer', $tokenValue)) {
            $this->addFlash(
                'error',
                'Token CSRF invalido. Una posible razón puede ser que haya tardado mucho tiempo en completar el formulario. Procure completarlo en menor tiempo.'
            );
            return $this->redirectToRoute('showReestablecerPassword',array('userID' => $userID, 'token' => $token));
        }
        if($post->get('user-password') != $post->get('user-re-password')){
            $this->addFlash(
                'error',
                'Las contraseñas son distintas. Vuelva a reescribir su contraseña.'
            );
            return $this->redirectToRoute('showReestablecerPassword',array('userID' => $userID, 'token' => $token));
        }

        $em = $this->getDoctrine()->getManager();
        $repositoryUsers = $em->getRepository('AppBundle:User');
        $user = $repositoryUsers->findOneById($userID);
        $password = $this->get('security.password_encoder')
            ->encodePassword($user, $post->get('user-password'));
        $user->setPassword($password);
        $user->setIsActive(true);
        $user->setToken(null);
        $em->flush();

        return $this->render('Utils/exito.html.twig', array(
            'mensaje' => 'Ahora puede ingresar utilizando su nueva contraseña',
            'callback' => $this->generateUrl('showLogin')
        ));
    }

    /**
     * @Route("/establecer/{userID}/{token}", name="showEstablecerPassword", requirements={"userID": "\d+"})
     */
    public function showEstablecerPasswordAction($userID = 0, $token = '')
    {
        $em = $this->getDoctrine()->getManager();
        $repositoryUsers = $em->getRepository('AppBundle:User');
        $user = $repositoryUsers->findOneById($userID);
        if(!$user){
            return $this->render('Utils/error.html.twig', array(
                'mensaje' => 'No existe el usuario',
                'callback' => $this->generateUrl('showIndex')
            ));
        }
        if($user->getToken() != $token){
            return $this->render('Utils/error.html.twig', array(
                'mensaje' => 'El token es invalido, no esta habilitado para reestablecer su contraseña',
                'callback' => $this->generateUrl('showIndex')
            ));
        }
        return $this->render('AdminCtrl/establecerPassword.html.twig', array(
            'userID' => $userID,
            'token' => $token
        ));
    }

    /**
     * @Route("/establecer/{userID}/{token}/run", name="runEstablecerPassword", requirements={"userID": "\d+"})
	 * @Method("POST")
     */
    public function runEstablecerPasswordAction($userID = 0, $token = '', Request $request)
    {
        $post = $request->request;
        $tokenValue = $post->get('csrf_token');
        //        $token = new CsrfToken($tokenId, $tokenValue);
        if (!$this->isCsrfTokenValid('formEstablecer', $tokenValue)) {
            $this->addFlash(
                'error',
                'Token CSRF invalido. Una posible razón puede ser que haya tardado mucho tiempo en completar el formulario. Procure completarlo en menor tiempo.'
            );
            return $this->redirectToRoute('showEstablecerPassword',array('userID' => $userID, 'token' => $token));
        }
        if($post->get('user-password') != $post->get('user-re-password')){
            $this->addFlash(
                'error',
                'Las contraseñas son distintas. Vuelva a reescribir su contraseña.'
            );
            return $this->redirectToRoute('showEstablecerPassword',array('userID' => $userID, 'token' => $token));
        }

        $em = $this->getDoctrine()->getManager();
        $repositoryUsers = $em->getRepository('AppBundle:User');
        $user = $repositoryUsers->findOneById($userID);
        $password = $this->get('security.password_encoder')
            ->encodePassword($user, $post->get('user-password'));
        $user->setPassword($password);
        $user->setIsActive(true);
        $user->setToken(null);
        $em->flush();

        return $this->render('Utils/exito.html.twig', array(
            'mensaje' => 'Ahora puede ingresar utilizando su nueva contraseña',
            'callback' => $this->generateUrl('showLogin')
        ));
    }
}
