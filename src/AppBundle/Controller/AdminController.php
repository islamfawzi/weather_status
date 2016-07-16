<?php
/**
 * Created by PhpStorm.
 * User: islam
 * Date: 7/15/16
 * Time: 1:44 AM
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\notes;
use AppBundle\Entity\users;
use Symfony\Component\HttpFoundation\Session\Session;

class AdminController extends Controller
{

    ////////////////////////////////////////////////// Admin Login //////////////////////////////////////////

    /**
     * @Route("/admincp/login", name="adminlogin")
     */
    public function login(){

        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('AdminController:login Function is Called');
        $logger->debug('@param - ');
        $logger->info("------------------------------");

        if(isset($_POST['submit_login'])){
            $logger->debug('-----------------------------');
            $logger->debug('login form submited ');
            $logger->debug('-----------------------------');


            $username = $_POST['username'];
            $password = $_POST['password'];

            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('AppBundle:users');

            /**
             * find logging in user
             */
            $user = $repository->findOneBy(
                array('username' => $username, 'password' => md5($password), 'status' => 1, 'usertype' => 1)
            );



            if($user){
                $logger->debug('-----------------------------');
                $logger->debug('Login Success ');
                $logger->debug('-----------------------------');

                /*
                 * update last visit Datetime
                 */
                $now = new \DateTime('now');
                $user->setLastvisitDate($now);
                $em->flush();

                /*
                 * set user session
                 */
                $session = new Session();
                $session->set('username', $user->getUsername());
                $session->set('fullname', $user->getFullname());

                return $this->render('admin/login.html.twig',
                    [
                        'logged' => 1,
                        'username' => $username
                    ]);
            }else{
                $logger->debug("------------------------");
                $logger->debug("Login Failed");
                $logger->debug("------------------------");

                return $this->render('admin/login.html.twig',
                    [
                        'logged' => 2,
                        'username' => $username
                    ]);
            }
        }
        return $this->render('admin/login.html.twig', ['logged' => 0]);
    }

    ////////////////////////////////////////////////// Index (Edit Notes) //////////////////////////////////////////

    /**
     * @Route("/admincp/", name="adminpage")
     */
    public function indexAction()
    {
        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('AdminController:indexAction Function is Called');
        $logger->debug('@param - ');
        $logger->info("------------------------------");

        $session = new Session();
        if(!$session->get('username')){
            $logger->debug("------------------------");
            $logger->debug("Not loggedin");
            $logger->debug("------------------------");

            return $this->redirectToRoute('adminlogin');
        }

        $notes = $this->getDoctrine()->getRepository("AppBundle:notes")->findAll();

        return $this->render('admin/notes_list.html.twig',
        ['notes' => $notes]);
    }

    ////////////////////////////////////////////////// Add Note //////////////////////////////////////////

    /**
     * @Route("/admincp/add-note", name="add-note")
     */
    public function add_note(){

        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('AdminController:add_note Function is Called');
        $logger->debug('@param - ');
        $logger->info("------------------------------");

        /**
         * check if logged in
         */
        $session = new Session();
        if(!$session->get('username')){
            $logger->debug("------------------------");
            $logger->debug("Not loggedin");
            $logger->debug("------------------------");

            return $this->redirectToRoute('adminlogin');
        }

        /**
         * add new note
         */
        if(isset($_POST['add_note_submit'])){
            $logger->debug('-----------------------------');
            $logger->debug('add note form submited ');
            $logger->debug('-----------------------------');

            $note = new notes();
            $now = new \DateTime('now');
            $note_date = new \DateTime($_POST['note_date']);
            /*
             * Bind data to note obj
             */
            $note->setTitle($_POST['title']);
            $note->setContent($_POST['content']);
            $note->setStatus($_POST['status']);
            $note->setNoteDate($note_date);
            $note->setCreateDate($now);
            $note->setPredefined($_POST['predefined']);

            /**
             * validate required fields
             */
            $validator = $this->get('validator');
            $errors = $validator->validate($note);

            if (count($errors) > 0) {

                $logger->debug("------------------------");
                $logger->debug("Add Note Validation Errors");
                $logger->debug("------------------------");

               return $this->render('admin/add_edit_notes.html.twig',
                    [
                        "page_index" => 1,
                        'title' => 'Add Note',
                        'note' => $note,
                        'errors' => $errors
                    ]);
            }

            /*
             * insert data into notes table
             */
            $em = $this->getDoctrine()->getManager();
            $em->persist($note);
            $em->flush();

            $this->addFlash('notice', 'Note Added Successfully');

            return $this->redirectToRoute('edit-note');

        }

        return $this->render('admin/add_edit_notes.html.twig',
            [
                "page_index" => 1,
                'title' => 'Add Note',
            ]);
    }

    ////////////////////////////////////////////////// Edit Note //////////////////////////////////////////

    /**
     * @Route("/admincp/edit-note/{note_id}", name="edit-note", defaults={"note_id" = 0})
     */
    public function edit_note($note_id){

        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('AdminController:edit_note Function is Called');
        $logger->debug('@param integer note_id ');
        $logger->info("------------------------------");

        $session = new Session();
        if(!$session->get('username')){
            $logger->debug("------------------------");
            $logger->debug("Not Logged in");
            $logger->debug("------------------------");

            return $this->redirectToRoute('adminlogin');
        }

        /*
         * get Notes List
         */
        if(!$note_id){

            /**
             * bulk delete notes
             */
            if(isset($_POST['notes_del'])){

                $logger->debug('-----------------------------');
                $logger->debug('Notes delete button clicked ');
                $logger->debug('-----------------------------');

                /*
                 * get selected notes to be delete
                 */
                $notes = $_POST['sel_notes'];
                foreach($notes as $note_id){
                    /**
                     * select notes by id
                     */
                    $em = $this->getDoctrine()->getManager();
                    $note = $em->getRepository('AppBundle:notes')->find($note_id);

                    /**
                     * flush removing notes
                     */
                    $em->remove($note);
                    $em->flush();
                }
                $this->addFlash('notice', 'Deleted Successfully');
                return $this->redirectToRoute('edit-note');

            }
            $notes = $this->getDoctrine()->getRepository("AppBundle:notes")->findAll();

            return $this->render('admin/notes_list.html.twig',
                ['notes' => $notes]);
        }
        /**
         * get note to edit by id
         */
        else{

            if(isset($_POST['edit_note_submit'])){
                $logger->debug('-----------------------------');
                $logger->debug('Edit Note form submited ');
                $logger->debug('-----------------------------');

                $now = new \DateTime('now');
                $note_date = new \DateTime($_POST['note_date']);

                /*
                 * get note which will updated
                 */
                $em = $this->getDoctrine()->getManager();
                $note = $em->getRepository('AppBundle:notes')->find($note_id);

                /*
                 * Bind data to note obj
                 */
                $note->setTitle($_POST['title']);
                $note->setContent($_POST['content']);
                $note->setStatus($_POST['status']);
                $note->setNoteDate($note_date);
                $note->setCreateDate($now);
                $note->setPredefined($_POST['predefined']);

                /**
                 * validate required fields
                 */
                $validator = $this->get('validator');
                $errors = $validator->validate($note);

                if (count($errors) > 0) {

                    $logger->debug("------------------------");
                    $logger->debug("Edit Note Validation Errors");
                    $logger->debug("------------------------");

                    return $this->render('admin/add_edit_notes.html.twig',
                        [
                            "page_index" => 2,
                            'title' => 'Edit Note',
                            'note' => $note,
                            'errors' => $errors
                        ]);
                }
                /*
                 * update data into notes table
                 */

                $em->flush();

                $this->addFlash('notice', 'Note updated Successfully');

                return $this->redirectToRoute('edit-note');

            }

            $note = $this->getDoctrine()->getRepository("AppBundle:notes")->find($note_id);

            return $this->render('admin/add_edit_notes.html.twig', ["page_index" => 2, 'note' => $note, 'title' => 'Edit Note']);
        }
    }


    /////////////////////////////////////////////// Add User /////////////////////////////////////

    /**
     * @Route("/admincp/add-user", name="add-user")
     */
    public function add_user(){

        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('AdminController:add_user Function is Called');
        $logger->debug('@param - ');
        $logger->info("------------------------------");

        $session = new Session();
        if(!$session->get('username')){
            $logger->debug("------------------------");
            $logger->debug("Not logged in");
            $logger->debug("------------------------");

            return $this->redirectToRoute('adminlogin');
        }

        /**
         * add new user
         */
        if(isset($_POST['add_user_submit'])){
            $logger->debug('-----------------------------');
            $logger->debug('Add User form submited ');
            $logger->debug('-----------------------------');

            $user = new users();
            /*
             * Bind data to user obj
             */
            $user->setUsername($_POST['username']);
            $user->setPassword(md5($_POST['password']));
            $user->setEmail($_POST['email']);
            $user->setMobile($_POST['mobile']);
            $user->setStatus($_POST['status']);
            $user->setUsertype($_POST['usertype']);
            $user->setLastvisitDate(new \DateTime($_POST['lastvisitDate']));
            $user->setFullname($_POST['fullname']);

            /**
             * validate required fields
             */
            $validator = $this->get('validator');
            $errors = $validator->validate($user);

            if (count($errors) > 0) {

                $logger->debug("------------------------");
                $logger->debug("Add User Validation Errors");
                $logger->debug("------------------------");

                return $this->render('admin/add_edit_users.html.twig',
                    [
                        "page_index" => 1,
                        'title' => 'Add User',
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

            $this->addFlash('notice', 'User Added Successfully');

            return $this->redirectToRoute('edit-user');

        }

        return $this->render('admin/add_edit_users.html.twig', ["page_index" => 1, 'title' => 'Add User']);
    }

    ////////////////////////////////////////////////// Edit User //////////////////////////////////////////

    /**
     * @Route("/admincp/edit-user/{user_id}", name="edit-user", defaults={"user_id" = 0})
     */
    public function edit_user($user_id){

        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('AdminController:edit_user Function is Called');
        $logger->debug('@param integer user_id');
        $logger->info("------------------------------");

        $session = new Session();
        if(!$session->get('username')){
            $logger->debug("------------------------");
            $logger->debug("Not Logged in");
            $logger->debug("------------------------");

            return $this->redirectToRoute('adminlogin');
        }

        /**
         * get Users List
         */
        if(!$user_id){

            /**
             * bulk delete notes
             */
            if(isset($_POST['users_del'])){

                $logger->debug('-----------------------------');
                $logger->debug('Delete Users button clicked');
                $logger->debug('-----------------------------');

                /*
                 * get selected notes to be delete
                 */
                $users = $_POST['sel_users'];
                foreach($users as $user_id){
                    /**
                     * select notes by id
                     */
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository('AppBundle:users')->find($user_id);

                    /**
                     * flush removing notes
                     */
                    $em->remove($user);
                    $em->flush();
                }
                $this->addFlash('notice', 'Deleted Successfully');
                return $this->redirectToRoute('edit-user');

            }
            $users = $this->getDoctrine()->getRepository("AppBundle:users")->findAll();

            return $this->render('admin/users_list.html.twig',
                ['users' => $users]);
        }
        /**
         * get user to update by id
         */
        else{

            if(isset($_POST['edit_user_submit'])){

                $logger->debug('-----------------------------');
                $logger->debug('Edit User form submited ');
                $logger->debug('-----------------------------');

                /*
                 * get note which will updated
                 */
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('AppBundle:users')->find($user_id);

                /*
                 * Bind data to user obj
                 */
                $user->setFullname($_POST['fullname']);
                $user->setUsername($_POST['username']);
                /**
                 * if password not changed keep it
                 */
                $logger->debug('-----------------------------');
                if(empty($_POST['password'])){
                    $logger->debug('Password not edited, set old password ');

                    $user->setPassword($user->getPassword());
                }else{
                    $logger->debug('New Password');
                    $user->setPassword(md5($_POST['password']));
                }
                $logger->debug('-----------------------------');

                $user->setEmail($_POST['email']);
                $user->setMobile($_POST['mobile']);
                $user->setStatus($_POST['status']);
                $user->setUsertype($_POST['usertype']);
                $user->setLastvisitDate(new \DateTime($_POST['lastvisitDate']));

                /**
                 * validate required fields
                 */
                $validator = $this->get('validator');
                $errors = $validator->validate($user);

                if (count($errors) > 0) {

                    $logger->debug("------------------------");
                    $logger->debug("Edit User Validation Errors");
                    $logger->debug("------------------------");

                    return $this->render('admin/add_edit_users.html.twig',
                        [
                            "page_index" => 2,
                            'title' => 'Edit User',
                            'user' => $user,
                            'errors' => $errors
                        ]);
                }

                /*
                 * update data into notes table
                 */

                $em->flush();

                $this->addFlash('notice', 'User updated Successfully');

                return $this->redirectToRoute('edit-user');

            }

            $user = $this->getDoctrine()->getRepository("AppBundle:users")->find($user_id);

            return $this->render('admin/add_edit_users.html.twig', ["page_index" => 2, 'user' => $user, 'title' => 'Edit User']);
        }
    }


////////////////////////////////////////////////// Admin Logout //////////////////////////////////////////

    /**
     * @Route("/admincp/logout", name="adminlogout")
     */
    public function logout(){

        $logger = $this->get('logger');
        $logger->info("------------------------------");
        $logger->info('AdminController:logout Function is Called');
        $logger->debug('@param - ');
        $logger->info("------------------------------");

        $session = new Session();
        $session->remove('username');
        $session->remove('fullname');
        return $this->redirectToRoute('adminlogin');
    }

}