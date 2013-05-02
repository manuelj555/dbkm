Vistas con Twig
===============

En esta rama del backend se implementa la libreria `Twig <http://twig.sensiolabs.org/>`_ para todas las vistas, Esto con la finalidad de aprovechas las ventajas de la implementación de dicha libreria como el autoescape, la herencia de plantillas (La facilidad de lectura y escritura de código para los desarrolladores), entre otras caracteristicas.

Variables Globales
__________________

* PUBLIC_PATH: contiene el valor de la constante php del mismo nombre
* APP_PATH: contiene el valor de la constante php del mismo nombre
* START_TIME: contiene el valor de la constante php del mismo nombre
* PRODUCTION: contiene el valor de la constante php del mismo nombre
* APP_CHARSET: contiene el valor de la constante php del mismo nombre
* is_ajax: contiene el valor retornado por la función Input::isAjax()

Funciones Añadidas
__________________

js(src, cache = true)
---------------------

Devuelve una etiqueta <script></script> para incluir el archivo javascript pasado por parametro, ejemplo:

.. code-block:: jinja

    <html>
        <head>
        </head>
        <body>
            ...
            {{ js('jquery/jquery.min') }}
            {{ js('jquery/jquery.kumbiaphp.min') }}
        </body>
    </html>

css(css, media = 'screen')
---------------------

Devuelve una etiqueta <style /> para incluir el archivo css pasado por parametro, ejemplo:

.. code-block:: jinja

    <html>
        <head>
            {{ css('bootstrap/bootstrap.min') }}
            {{ css('backend/estilos') }}
        </head>
        <body>
            ...
        </body>
    </html>

path(path, isAction = false)
---------------------

Devuelve una url con el PUBLIC_PATH delante, si se pasa true en el isAction, asumirá que el path es solo un action, y completará el modulo y controlador.

.. code-block:: jinja

    <html>
        <head>
        </head>
        <body>
            <a href="{{ path('') }}">Inicio</a>
            <a href="{{ path('/nosotros') }}">Quienes Somos</a>
            <a href="{{ path('/portafolio') }}">Portafolio</a>
        </body>
    </html>

Macros
______
