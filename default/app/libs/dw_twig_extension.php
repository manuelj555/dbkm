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

    public function initRuntime(Twig_Environment $twig)
    {
        $twig->addGlobal('dw', $twig->loadTemplate('macros.twig'));
    }

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
            'button_class' => new Twig_Function_Method($this, 'prepareButtonClass'),
            'security_key' => new Twig_Function_Function("DwSecurity::getKey", array('is_safe' => $safe)),
            'attrs' => new Twig_Function_Method($this, 'attrs', array('is_safe' => $safe)),
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

    public function prepareButtonClass($attrs, $bold = false)
    {
        if (isset($attrs['class'])) {
            $class = " {$attrs['class']} "; //para poder buscar espacios a los lados
        } else {
            $class = '';
        }

        if (APP_AJAX) {
            if (!trim($class)) {
                $class = 'dw-ajax dw-spinner';
            } else {
                if (false === stripos($class, ' no-ajax ')) {
                    $class .= ' dw-ajax';
                }
                if (false === stripos($class, ' no-spinner ')) {
                    $class .= ' dw-spinner';
                }
            }
        }

        if ($bold) {
            if (false === stripos($class, ' dw-text-bold ')) {
                $class .= ' dw-text-bold';
            }
        }

        return trim($class);
    }

    public function attrs($attrs, $unset)
    {
        $string = '';
        $unset = (array) $unset;

        foreach ((array) $attrs as $attribute => $value) {
            if (!in_array($attribute, $unset)) {
                $string .= $attribute . '="' . $value . '" ';
            }
        }

        return $string;
    }

}

