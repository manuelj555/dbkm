<?php

/**
 * @see KumbiaView
 */
require_once CORE_PATH . 'kumbia/kumbia_view.php';
require_once APP_PATH . 'libs/Twig/lib/Twig/Autoloader.php';

/**
 * Esta clase permite extender o modificar la clase ViewBase de Kumbiaphp.
 *
 * @category KumbiaPHP
 * @package View
 */
class View extends KumbiaView
{

    /**
     * Método que muestra el contenido de una vista
     */
    public static function content()
    {
        //Verifico si hay mensajes
        if (DwMessage::has()) {
            DwMessage::output();
        }
        parent::content();
    }

    /**
     * Método para mostrar los mensajes e impresiones del request
     */
    public static function notify($unique = true)
    {
        if ($unique) {
            return self::partial('dw_message');
        } else {
            return self::partial('dw_message', false, array('id' => rand(1, 5000)));
        }
    }

    /**
     * Método para mostrar el proceso actual en las vistar
     */
    public static function process($modulo, $proceso = null, $title = true)
    {
        return self::partial('dw_process', false, array('modulo' => $modulo, 'proceso' => $proceso, 'titulo' => $title));
    }

    /**
     * Método para las execpciones
     */
    public static function exception(KumbiaException $e)
    {
        if (PRODUCTION) {
            $counter = (Session::has('exception_counter')) ? Session::get('exception_counter') : 1;
            Session::set('exception_counter', $counter++);
            DwMessage::warning('Oops! hemos realizado algún procedimiento mal... <br />Inténtalo nuevamente!');
        } else {
            DwMessage::error($e->getMessage());
            DwMessage::error("Detalle del error: " . $e->getTraceAsString());
        }
        if (Session::has('exception_counter')) {
            if (Session::get('exception_counter') > 2) {
                DwMessage::info('Si el problema persiste contacta con el administrador del sistema.');
                Sesion::set('exception_counter', 0);
            }
        }
    }

    /**
     * Método que muestra una vista para redireccionar si se trabaja con ajax la app
     */
    public static function redirect($url)
    {
        View::select(NULL, NULL);
        return self::partial('dw_redirect', false, array('url' => $url));
    }

    /**
     * Método para redireccionar usando AJAX hacia el login
     * @TODO: revisar que reciba la dirección
     */
    public static function redirectToLogin($url = NULL)
    {
        self::$_path = '_shared/errors/';
        self::select('dw_session', null);
    }

    /**
     * Método para mostrar la respuesta de la vista
     * de una petición con ajax
     * @return boolean false
     */
    public static function ajax()
    {
        self::getTwig()->render('@partials/dw_message.twig');
        return false;
    }

    /**
     * Método para mostrar el mensaje de actualizacion
     */
    public static function update()
    {
        self::$_path = '_shared/errors/';
        self::select('dw_update');
    }

    /**
     * Método para mostrar un json
     */
    public static function json($data = array())
    {
        View::select(NULL, NULL);
        echo json_encode($data);
    }

    /**
     * Método que muestra el reporte según el formato. Si es un formato desconocido muesra la página de error
     *
     * @param string $formato Formato a mostrar: html, pdf, xls, xml, ticket, etc
     * @return boolean
     */
    public static function report($formato)
    {
        $formato = Filter::get($formato, 'string');
        $template = ($formato == 'html') ? 'backend/impress' : NULL;
        if ($formato == 'error') {
            self::error();
        } else if (($formato != 'html' && $formato != 'pdf' && $formato != 'xls' && $formato != 'xlsx' && $formato != 'doc' && $formato != 'docx' && $formato != 'csv' && $formato != 'xml' && $formato != 'ticket') or $formato == null) {
            DwMessage::error('Error: ACCESO DENEGADO. El formato del reporte es incorrecto.');
            self::error();
        } else {
            self::response($formato, $template);
        }
    }

    /**
     * Método para mostrar una ventana de error
     */
    public static function error($template = 'backend/error')
    {
        self::$_path = '_shared/errors/';
        self::select('popup', $template);
    }

    public static function render($controller, $_url)
    {
        // Guarda el controlador actual
        self::$_controller = $controller;

        $file = self::$_path . self::$_view;
        if (is_file(APP_PATH . 'views/' . ltrim($file, '/') . '.twig')) {
            //si existe el archivo .twig iniciamos la lib Twig y renderizamos la vista
            self::$_content = ob_get_clean();

            self::getTwig()->display($file . '.twig', self::getVar());
        } else {
            //si no se encuentra el archivo .twig carga el render del fw
            return parent::render($controller, $_url);
        }
    }

    public static function getTwig()
    {
        static $twig;
        if (!$twig) {
            Twig_Autoloader::register();

            $loader = new Twig_Loader_Filesystem(array());

            $loader->addPath(APP_PATH . 'views/_shared/templates', 'templates');
            $loader->addPath(APP_PATH . 'views/_shared/partials', 'partials');
            $loader->addPath(APP_PATH . 'views/_shared/scaffolds', 'scaffolds');
            $loader->addPath(APP_PATH . 'views/_shared/errors', 'errors');
            $loader->addPath(APP_PATH . 'views');

            $twig = new Twig_Environment($loader, array(
                'cache' => APP_PATH . 'temp/cache/twig',
                'debug' => !PRODUCTION,
                'strict_variables' => true,
            ));

            $twig->addExtension(new Twig_Extension_Debug());
            $twig->addExtension(new DwTwigExtension());
            $twig->addExtension(new TwigExtension());
        }
        return $twig;
    }

}
