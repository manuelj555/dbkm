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

security_key(id, action='')
-------------

Crea una llave de seguridad para enviar como un parametro de la url, generalmente es usada para enviar id de registros encriptados.

.. code-block:: jinja

    <html>
        <head>
        </head>
        <body>
            <a href="{{ path("editar/" ~ security_key(id), true) }}" >Editar Registro {{ id }}</a>
        </body>
    </html>

attrs(attrs, unset = null)
-------------

A partir de un arreglo con pares clave: valor, crea un string de la forma clave="valor" para ser usado generalmente como atributos de una etiqueta html, la variable unset se usa para pasar un arreglo de los indices a excluir en la creación del string, ejemplo:

.. code-block:: jinja

    <html>
        <head>
        </head>
        <body>
            <a href="{{ path("inicio") }} {{ attrs({ id: 'link_inicio', title: 'Mi Pagina de Inicio' }) }}" >Inicio</a>
            <a href="{{ path("contacto") }} class="contactos" {{ attrs({ class: 'mi_clase', title: 'Mi Pagina de Contacto' }) }}" >Inicio</a>
        </body>
    </html>

button_class(attrs, bold = false)
-------------

Recibe un arreglo con los attrs y busca el indice class si existe, y Devuelve un string con las clases basicas usadas en el backend para los botones, las clases a devolver son:

* dw-ajax: se devuelve si el arreglo contiene el indice class y dicho indice no tiene en su string la clase no-ajax.
* dw-spinner: se devuelve si el arreglo contiene el indice class y dicho indice no tiene en su string la clase no-spinner.
* dw-text-bold: se devuelve si se especifica el segundo parametro de la funcion a true.

.. code-block:: jinja

    <html>
        <head>
        </head>
        <body>
            <a href="{{ path("contacto") }} class="btn {{ button_class({{ class: 'no-ajax' }}, true) }}">Inicio</a>
            Devuelve : 
            <a href="/localhost/contacto" class="btn dw-spinner dw-text-bold">Inicio</a>
        </body>
    </html>

current_url()
-------------

Devuelve la url actual completa con todo y parametros (menos el parametro pag.* de los paginadores).

.. code-block:: jinja

    <html>
        <head>
        </head>
        <body>
            Pagina Actual : {{ current_url() }}
        </body>
    </html>

Macros
______

Las Macros son funciones creadas en un archivo.twig y se encuentra en app/views/macros.twig y se acceden a la misma en esta aplicación dentro de la variable dw.

dw.paginator(page, url)
-------------

Crea un paginador para un arreglo de registros especificados en page, con la url para los links pasada como segundo parametro.

.. code-block:: jinja

    <html>
        <head>
        </head>
        <body>
            Listado
            ...
            {{ dw.paginator(usuarios, 'sistema/usuarios/index') }}
        </body>
    </html>

dw.grid(form_action, order_action)
-------------

Crea el javascript usado para darle funcionalidad de la funcion javascript dwGrid a las tablas del backend.

Si el primer parametro es null, no se usa buscador.

.. code-block:: jinja

    <html>
        <head>
        </head>
        <body>
            Listado
            ...
            {{ dw.grid('url_hacia_el_buscador', 'url_para_el_orden') }}
        </body>
    </html>

dw.page_header(page_module, page_title)
-------------

Usado para crear los titulos de las paginas usando los estilos del bootstrap.

.. code-block:: jinja

    <html>
        <head>
        </head>
        <body>
            {{ dw.page_header('Usuarios', 'Listado') }}
            Devuelve:
            <div class="page-header">
                <h4>Usuarios |<small> Listado</small></h4>
            </div>

            {{ dw.page_header('Menus', 'Listado | Menus del Sistema') }}
            Devuelve:
            <div class="page-header">
                <h4>Menus |<small> Listado</small></h4>
            </div>

            {{ dw.page_header('Auditorias') }}
            Devuelve:
            <div class="page-header">
                <h4>Auditorias</h4>
            </div>
        </body>
    </html>

dw.messages(id)
-------------

Llama a la funcion View::content() del framework para mostrar los mensajes flash

.. code-block:: jinja

    <html>
        <head>
        </head>
        <body>
            <header></header>
            {{ dw.messages() }} devuelve <div id="dw-message" class="dw-message">{Los mensajes}</div>
            <section>
            </section>
            <footer></footer>
        </body>
    </html>


