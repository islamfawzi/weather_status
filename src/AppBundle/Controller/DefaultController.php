<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\users;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class DefaultController extends Controller
{
    /////////////////////////////////// Index /////////////////////////////////////
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('DefaultController:indexAction Function is Called');
        $logger->debug('@param - ');
        $logger->info("------------------------------");

        try{
            $client = new \SoapClient("http://www.webservicex.net/globalweather.asmx?WSDL");
            $soap_result = $client->GetWeather(array('CityName' => 'Cairo', 'CountryName' => 'Egypt'));

            /*
             * handle xml result
             * cast from std to object
             * convert label from utf-16 to utf-8
             * parse xml document into object
             */
            if($soap_result){
                $xml = (string)$soap_result->GetWeatherResult;
                $xml = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xml);
                $weather_status = simplexml_load_string(utf8_encode($xml)) or die("Error: Cannot create object");
            }else{
                $logger->error("------------------------------");
                $logger->error("webservicex: No Result");
                $logger->error("------------------------------");
            }
        }
        catch(\Exception $ex){
          // Log
            $logger->error("------------------------------");
            $logger->error("webservicex: " . $ex->getMessage());
            $logger->error("------------------------------");
        }

        $date = new \DateTime('today');
        $repository = $this->getDoctrine()->getRepository('AppBundle:notes');

        $today_notes = $repository->findBy(
             array('noteDate'=>$date, 'status' => 1)
        );

        if(!$today_notes){
            $logger->debug("------------------------------");
            $logger->debug("No Notes for today");
            $logger->debug("------------------------------");
         /**
          * extract temperature from string
          */
          $temperature =  $weather_status->Temperature;
          $start = strpos($temperature, '(') + 1;
          $len = strpos($temperature,' ',$start) - $start;
          $temperature = substr($temperature, $start, $len);

          if($temperature > 1 && $temperature <= 10)
          {
              $logger->debug('-----------------------------');
              $logger->debug('Get Predefined Note for range 1 < value <= 10');
              $logger->debug('-----------------------------');

              $today_notes = $repository->findBy(
                  array('predefined' => 1, 'status' => 1)
              );
          }
          elseif($temperature > 10 && $temperature <= 15)
          {
              $logger->debug('-----------------------------');
              $logger->debug('Get Predefined Note for range 10 < value <= 15');
              $logger->debug('-----------------------------');

              $today_notes = $repository->findBy(
                  array('predefined' => 2, 'status' => 1)
              );
          }
          elseif($temperature > 15 && $temperature <= 20)
          {
              $logger->debug('-----------------------------');
              $logger->debug('Get Predefined Note for range 15 < value <= 20');
              $logger->debug('-----------------------------');

              $today_notes = $repository->findBy(
                  array('predefined' => 3, 'status' => 1)
              );
          }
          elseif($temperature > 20)
          {
              $logger->debug('-----------------------------');
              $logger->debug('Get Predefined Note for range 20 < value');
              $logger->debug('-----------------------------');

              $today_notes = $repository->findBy(
                  array('predefined' => 4, 'status' => 1)
              );
          }
        }



        return $this->render('default/index.html.twig',
        [
            'weather_status' => $weather_status,
            'notes' => $today_notes,
        ]);
    }

    /////////////////////////////////// Register /////////////////////////////////////
    /**
     * @Route("/register", name="register")
     */
    public function register()
    {
        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('DefaultController:register Function is Called');
        $logger->debug('@param - ');
        $logger->info("------------------------------");

        if(isset($_POST['register_submit'])){

            $logger->debug('-----------------------------');
            $logger->debug('Register Form Submited');
            $logger->debug('-----------------------------');

            $user = new users();

            /*
             * Bind data to user obj
             */
            $user->setUsername($_POST['username']);
            $user->setPassword(md5($_POST['password']));
            $user->setEmail($_POST['email']);
            $user->setMobile($_POST['mobile']);
            $user->setStatus(1);
            $user->setUsertype(2);
            $user->setLastvisitDate(new \DateTime('now'));
            $user->setFullname($_POST['username']);

            /**
             * validate required fields
             */
            $validator = $this->get('validator');
            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                $logger->debug("------------------------------");
                $logger->debug("Registeration: Validation error");
                $logger->debug("------------------------------");

                return $this->render('default/register.html.twig',
                    [
                        'user' => $user,
                        'errors' => $errors
                    ]);
            }

            /*
             * insert data into notes table
             */
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('notice', 'Gongratulations, you have registered Successfully');
            $session = new Session();
            $session->set('username', $_POST['username']);
            $session->set('fullname', $_POST['username']);

            return $this->redirectToRoute('register');

        }

        return $this->render('default/register.html.twig');
    }

    /////////////////////////////////// Login /////////////////////////////////////
    /**
     * @Route("/login", name="login")
     */
    public function login()
    {
        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('DefaultController:login Function is Called');
        $logger->debug('@param - ');
        $logger->info("------------------------------");

        $email = $_POST['email'];
        $password = $_POST['password'];

        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('AppBundle:users');

        /**
         * find logging in user
         */
        $user = $repository->findOneBy(
            array('email' => $email, 'password' => md5($password), 'status' => 1)
        );

        if($user){
            $logger->debug('-----------------------------');
            $logger->debug('Login Success');
            $logger->debug('-----------------------------');

            /*
             * update last visit Datetime
             */
            $now = new \DateTime('now');
            $user->setLastvisitDate($now);
            $em->flush();

            $session = new Session();
            $session->set('username', $user->getUsername());
            $session->set('fullname', $user->getFullname());
            $session->set('usertype', $user->getUsertype());
        }
        else{
            $logger->debug("------------------------------");
            $logger->debug("Login Failed, Email or password are invalid");
            $logger->debug("------------------------------");

            $this->addFlash('fail', 'Email or password are invalid');
        }
        return $this->redirectToRoute('homepage');

    }

    /////////////////////////////////// Logout /////////////////////////////////////
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('DefaultController:logout Function is Called');
        $logger->debug('@param - ');
        $logger->info("------------------------------");

        $session = new Session();
        $session->remove('username');
        $session->remove('fullname');
        $session->remove('usertype');
        return $this->redirectToRoute('homepage');
    }


    /////////////////////////////////// for Ajax Request /////////////////////////////////////

    /**
     * @Route("/getnote/{note_id}", name="get-note", defaults={"note_id" = 0})
     */
    public function get_note($note_id)
    {
        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('DefaultController:get_note Function is Called');
        $logger->debug('@param string note_id ');
        $logger->info("------------------------------");

        $note_id = $_POST['note_id'];

        if($note_id == 'temp'){

            $logger->debug('-----------------------------');
            $logger->debug('get Weather status (Ajax Request)');
            $logger->debug('-----------------------------');


            $client = new \SoapClient("http://www.webservicex.net/globalweather.asmx?WSDL");
            $soap_result = $client->GetWeather(array('CityName' => 'Cairo', 'CountryName' => 'Egypt'));

            $xml = (string)$soap_result->GetWeatherResult;
            $xml = preg_replace('/(<\?xml[^?]+?)utf-16/i', '$1utf-8', $xml);
            $weather_status = simplexml_load_string(utf8_encode($xml)) or die("Error: Cannot create object");

            $view = $this->render('default/weather.html.twig',
            [
                'weather_status' => $weather_status
            ]);
        }
        else{
            $logger->debug('-----------------------------');
            $logger->debug('get Note by Id (Ajax Request)');
            $logger->debug('-----------------------------');

            $repository = $this->getDoctrine()->getRepository('AppBundle:notes');
            $note = $repository->find($note_id);

            $view = $this->render('default/note.html.twig',
            [
                'note' => $note
            ]);
        }

        return $view;
    }
}
