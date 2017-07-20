<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User as User;
use AppBundle\Entity\Fecha as Fecha;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminCtrl extends Controller
{

    /**
     * @Route("/admin", name="showDashboard")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function showDashboardAction(Request $request)
    {
$em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT COUNT(r.id)
         FROM AppBundle:Reserva r
         WHERE r.deletedAt is null
		 '
        );
        $cantReservas = $query->getSingleScalarResult();
        $query = $em->createQuery(
            'SELECT COALESCE(SUM(r.cupo),0)
         FROM AppBundle:Reserva r
         WHERE r.deletedAt is null
		 '
        );
        $cantAlumnos = $query->getSingleScalarResult();
        $query = $em->createQuery(
            'SELECT COUNT(f.id)
         FROM AppBundle:Fecha f
		 '
        );
        $cantFechas = $query->getSingleScalarResult();
        return $this->render('AdminCtrl/admin/showDashboard.html.twig', [
            'cantReservas' => $cantReservas,
            'cantAlumnos' => $cantAlumnos,
            'cantFechas' => $cantFechas
        ]);
    }

    /**
     * @Route("/fecha/listar", name="showFechas")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function showFechasAction()
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Fecha');
        $fechas = $repo->findAll();
        return $this->render('AdminCtrl/admin/gestionar/showFechas.html.twig', [
            'fechas' => $fechas,
        ]);
    }

    /**
     * @Route("/fecha/crear", name="showCrearFecha")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function showCrearFechaAction()
    {
        return $this->render('AdminCtrl/admin/gestionar/showCrearFechas.html.twig', [
        ]);
    }

    /**
     * @Route("/fecha/crear", name="runCrearFecha")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("POST")
     */
    public function runCrearFechaAction(Request $request)
    {
        // Lets get the request
        $post = $request->request;
        // Now lets make a new Sala without data.
        $fecha = new Fecha();

        $tokenValue = $post->get('csrf_token');
        if (!$this->isCsrfTokenValid('formCrearFecha', $tokenValue)) {
            $this->addFlash(
                'error',
                'Token CSRF invalido'
            );
            return $this->redirectToRoute('showCrearFecha');
        }
        $fecha->setFecha(new \DateTime($post->get('reserva-dia')));
        $fecha->setHora(new \DateTime($post->get('reserva-hora')));
        $fecha->setMetafecha(new \DateTime($post->get('reserva-dia') . ' ' . $post->get('reserva-hora')));
        $fecha->setCupos($post->get('reserva-cupo'));
        $fecha->setCuposRestantes($post->get('reserva-cupo'));

        // save the User!
        $em = $this->getDoctrine()->getManager();
        $em->persist($fecha);
        $em->flush();


        // Lets show a success!
        $this->addFlash(
            'success',
            'Se ha creado una nueva fecha y horario para recibir visitas! Awesome!'
        );
        return $this->redirectToRoute('showFechas');

    }

    /**
     * @Route("/admin/listar", name="showAdmins")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function showAdminsAction()
    {
      $repo = $this->getDoctrine()->getRepository('AppBundle:User');
        $admins = $repo->findAll();
        return $this->render('AdminCtrl/admin/gestionar/showAdmins.html.twig', [
            'administradores' => $admins,
        ]);
    }

    /**
     * @Route("/admin/crear", name="showCrearAdmin")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function showCrearAdminAction()
    {

        return $this->render('AdminCtrl/admin/gestionar/showCrearAdmin.html.twig', [

        ]);
    }

    /**
     * @Route("/admin/crear", name="runNuevoAdmin")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("POST")
     */
    public function runCrearAdminAction(Request $request)
    {

        // Lets get the request
        $post = $request->request;
        // Now lets make a new Sala without data.
        $user = new User();

        $tokenValue = $post->get('csrf_token');
        if (!$this->isCsrfTokenValid('formCrearAdmin', $tokenValue)) {
            $this->addFlash(
                'error',
                'Token CSRF invalido'
            );
            return $this->redirectToRoute('showCrearAdmin');
        }

        $password = $this->get('security.password_encoder')
            ->encodePassword($user, openssl_random_pseudo_bytes(16));
        $user->setPassword($password);
        $user->setUsername($post->get('admin-email'));
        $user->setEmail($post->get('admin-email'));
        $user->setNombre($post->get('admin-nombre'));
        $user->setApellido($post->get('admin-apellido'));
        $user->setRole('ROLE_ADMIN');
        $user->setIsActive(false);
        $emailToken = bin2hex(openssl_random_pseudo_bytes(16));
        $user->setToken($emailToken);

        // 4) save the User!
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $urlEstablecer = $this->generateUrl(
            'showEstablecerPassword',
            array('userID' => $user->getID(), 'token' => $emailToken), UrlGeneratorInterface::ABSOLUTE_URL
        );
        $message = \Swift_Message::newInstance()
            ->setSubject('Datos de acceso al panel de administración')
            ->setFrom(array('ingreso@frsf.utn.edu.ar' => 'UTN Abierta'))
            ->setTo($user->getEmail())
            ->setBody(
            $this->renderView(
                'email/nuevoAdmin.html.twig',
                array('user' => $user, 'link' => $urlEstablecer )
            ),
            'text/html'
        );

        $this->get('mailer')->send($message);
        // Lets show a success!
        $this->addFlash(
            'success',
            'El administrador ha sido creado. Se ha enviado un mail a ' . $user->getEmail() .' con los datos para iniciar sesión'
        );
        return $this->redirectToRoute('showCrearAdmin');

    }

    /**
     * @Route("/reserva/listar", name="showReservas")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function showReservasAction()
    {
      $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT f.fecha
         FROM AppBundle:Fecha f
         GROUP BY f.fecha
         ORDER BY f.fecha ASC
		 '
        );
        $dias = $query->getResult();
        $query = $em->createQuery(
            'SELECT DISTINCT(f.fecha)
         FROM AppBundle:Fecha f
         ORDER BY f.fecha ASC
		 '
        );
        $diasString = $query->getArrayResult();
        $diasString = array_column($diasString,1);
        $fechas = array();
        foreach($diasString as $dia){
        $query = $em->createQuery(
            'SELECT f,r,i
         FROM AppBundle:Fecha f
         LEFT JOIN f.reservas r
         LEFT JOIN r.institucion i
         INDEX BY f.hora
         WHERE f.fecha = ?1
         AND r.deletedAt is null
         ORDER BY f.hora ASC
		 '
        );
        $query->setParameter('1', $dia );
        $horarios = $query->getResult();
        $fechas[$dia] = $horarios;
        }
        return $this->render('AdminCtrl/admin/reservas/showReservas.html.twig', [
            'fechas' => $fechas,
            'diasString' => $diasString,
            'dias' => $dias
        ]);
    }

     /**
     * @Route("/reserva/listar/print", name="printReservas")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function printReservasAction()
    {
      $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT f.fecha
         FROM AppBundle:Fecha f
         GROUP BY f.fecha
         ORDER BY f.fecha ASC
		 '
        );
        $dias = $query->getResult();
        $query = $em->createQuery(
            'SELECT DISTINCT(f.fecha)
         FROM AppBundle:Fecha f
         ORDER BY f.fecha ASC
		 '
        );
        $diasString = $query->getArrayResult();
        $diasString = array_column($diasString,1);
        $fechas = array();
        foreach($diasString as $dia){
        $query = $em->createQuery(
            'SELECT f,r,i
         FROM AppBundle:Fecha f
         LEFT JOIN f.reservas r
         LEFT JOIN r.institucion i
         INDEX BY f.hora
         WHERE f.fecha = ?1
         AND r.deletedAt is null
         ORDER BY f.hora ASC
		 '
        );
        $query->setParameter('1', $dia );
        $horarios = $query->getResult();
        $fechas[$dia] = $horarios;
        }
        return $this->render('AdminCtrl/admin/reservas/printReservas.html.twig', [
            'fechas' => $fechas,
            'diasString' => $diasString,
            'dias' => $dias
        ]);
    }

    /**
     * @Route("/reserva/institucion/listar", name="showInstituciones")
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function showInstitucionesAction()
    {
$repo = $this->getDoctrine()->getRepository('AppBundle:Institucion');
        $instituciones = $repo->findAll();
        return $this->render('AdminCtrl/admin/reservas/showInstituciones.html.twig', [
            'instituciones' => $instituciones
        ]);
    }

     /**
     * @Route("/reserva/institucion/{institucionID}", name="showInstitucion", requirements={"institucionID": "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function showInstitucionAction($institucionID = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Institucion');
        $institucion = $repo->findOneById($institucionID);
        return $this->render('AdminCtrl/admin/reservas/showInstitucion.html.twig', [
            'institucion' => $institucion
        ]);
    }

    /**
     * @Route("/reserva/{reservaID}", name="showReservaInstitucion", requirements={"reservaID": "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("GET")
     */
    public function showReservaInstitucionAction($reservaID = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Reserva');
        $reserva = $repo->findOneById($reservaID);
         if ($reserva->getFecha() == null) {
            $this->addFlash(
                'error',
                'Se ha cancelado la reserva'
            );
            return $this->redirectToRoute('showReservas');
        }
        $query = $em->createQuery(
            'SELECT f
         FROM AppBundle:Fecha f
         LEFT JOIN f.reservas r
         LEFT JOIN r.institucion i
         WHERE f.cuposRestantes >= ?1
         ORDER BY f.fecha, f.hora ASC
		 '
        );
        $query->setParameter('1', $reserva->getCupo() );
        $fechas = $query->getResult();
        return $this->render('AdminCtrl/admin/reservas/showReserva.html.twig', [
            'reserva' => $reserva,
            'fechas' => $fechas
        ]);
    }

    /**
     * @Route("/reserva/{reservaID}/cancelar", name="runCancelarReserva", requirements={"reservaID": "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("POST")
     */
    public function runCancelarReservaAction(Request $request, $reservaID)
    {

        // Lets get the request
        $post = $request->request;

        $tokenValue = $post->get('csrf_token');
        if (!$this->isCsrfTokenValid('formCancelarReserva', $tokenValue)) {
            $this->addFlash(
                'error',
                'Token CSRF invalido'
            );
            return $this->redirectToRoute('showReservaInstitucion', array('reservaID' => $reservaID));
        }

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Reserva');
        $reserva = $repo->findOneById($reservaID);

        $reserva->setDeletedAt(new \DateTime());
        $fecha = $reserva->getFecha();
        $fecha->setCuposRestantes( $fecha->getCuposRestantes() + $reserva->getCupo() );
        $fecha->removeReserva($reserva);
        $reserva->setFecha(null);

        $em->flush();

        $this->addFlash(
            'success',
            'La reserva ha sido cancelada'
        );
        return $this->redirectToRoute('showReservas');
    }

    /**
     * @Route("/reserva/{reservaID}/cambiar-cupo", name="runCambiarCupo", requirements={"reservaID": "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("POST")
     */
    public function runCambiarCupoAction(Request $request, $reservaID)
    {

        // Lets get the request
        $post = $request->request;

        $tokenValue = $post->get('csrf_token');
        if (!$this->isCsrfTokenValid('formCambiarCupo', $tokenValue)) {
            $this->addFlash(
                'error',
                'Token CSRF invalido'
            );
            return $this->redirectToRoute('showReservaInstitucion', array('reservaID' => $reservaID));
        }

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Reserva');
        $reserva = $repo->findOneById($reservaID);

        $fecha = $reserva->getFecha();
        $fecha->setCuposRestantes( ($fecha->getCuposRestantes() + $reserva->getCupo()) - $post->get('reserva-cupo') );
        $reserva->setCupo($post->get('reserva-cupo'));

        $em->flush();

        $this->addFlash(
            'success',
            'Se cambio el cupo de la reserva'
        );
        return $this->redirectToRoute('showReservaInstitucion', array('reservaID' => $reservaID));
    }

     /**
     * @Route("/reserva/{reservaID}/cambiar-fecha", name="runCambiarFecha", requirements={"reservaID": "\d+"})
     * @Security("has_role('ROLE_ADMIN')")
     * @Method("POST")
     */
    public function runCambiarFechaAction(Request $request, $reservaID)
    {

        // Lets get the request
        $post = $request->request;

        $tokenValue = $post->get('csrf_token');
        if (!$this->isCsrfTokenValid('formCambiarFecha', $tokenValue)) {
            $this->addFlash(
                'error',
                'Token CSRF invalido'
            );
            return $this->redirectToRoute('showReservaInstitucion', array('reservaID' => $reservaID));
        }

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Reserva');
        $reserva = $repo->findOneById($reservaID);
        $repo = $em->getRepository('AppBundle:Fecha');
        $fechaNueva = $repo->findOneById($post->get('reserva-fechaID'));

        $fecha = $reserva->getFecha();
        $fecha->setCuposRestantes( $fecha->getCuposRestantes() + $reserva->getCupo() );
        $fecha->removeReserva($reserva);

        $fechaNueva->setCuposRestantes( $fechaNueva->getCuposRestantes() - $reserva->getCupo() );
        $reserva->setFecha($fechaNueva);

        $em->flush();

        $this->addFlash(
            'success',
            'Se cambió la fecha de la reserva'
        );
        return $this->redirectToRoute('showReservaInstitucion', array('reservaID' => $reservaID));
    }

}
