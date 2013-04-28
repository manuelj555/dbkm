<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dw_twig_extension
 *
 * @author manuel
 */
class DwTwigExtension extends Twig_Extension
{

    public function getName()
    {
        return 'dw_backend_extesion';
    }

    public function getFunctions()
    {
        $safe = array('html', 'js', 'css');
        return array(
            'form_*' => new Twig_Function_Method($this, 'form', array('is_safe' => $safe)),
            'html_*' => new Twig_Function_Method($this, 'html', array('is_safe' => $safe)),
            'menu' => new Twig_Function_Method($this, 'menu', array('is_safe' => $safe)),
            'user_*' => new Twig_Function_Method($this, 'user'),
            'current_url' => new Twig_Function_Method($this, 'currentUrl'),
            'security_key' => new Twig_Function_Function("DwSecurity::getKey", array('is_safe' => $safe)),
        );
    }

    public function getGlobals()
    {
        return array(
        );
    }

    public function helper($class, $method, $args)
    {
        unset($args[0]);
        return call_user_func_array("{$class}::{$method}", $args);
    }

    public function form($method)
    {
        return $this->helper('DwForm', $method, func_get_args());
    }

    public function html($method)
    {
        return $this->helper('DwHtml', $method, func_get_args());
    }

    public function menu($entorno)
    {
        return DwMenu::load($entorno, Session::get('perfil_id'));
    }

    public function user($index)
    {
        return Session::get($index);
    }

    public function currentUrl()
    {
        extract(Router::get());
        $url = "$controller/$action";
        if ($module) {
            $url = "$module/$url";
        }
        if ($parameters) {
            $params = join('/', $parameters);
            $params = explode('pag', $params);
            if ($params[0]) {
                $url = "$url/" . trim($params[0], '/');
            }
        }
        return trim($url, '/');
    }

}

