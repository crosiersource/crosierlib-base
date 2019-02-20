<?php
/**
 * Created by PhpStorm.
 * User: carlos
 * Date: 25/01/19
 * Time: 11:17
 */

namespace CrosierSource\CrosierLibBaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Interface CrosierDefaultController.
 *
 * CONVENÇÃO: Todos os CrosierApps devem ter um DefaultController implementando esta interface.
 *
 * @package CrosierSource\CrosierLibBaseBundle\Controller
 * @author Carlos Eduardo Pauluk
 */
abstract class CrosierDefaultController extends AbstractController
{

    /**
     * @return mixed
     */
    public abstract function index();

    /**
     *
     * @return mixed
     */
    public abstract function getAppMainMenu();

}