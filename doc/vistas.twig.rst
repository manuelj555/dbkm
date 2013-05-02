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

.. code-block:: php

    <html>
        <head>
            {{ js('jquery/jquery.min') }}
        </head>
        <body>
            ...
        </body>
    </html>

Macros
______
