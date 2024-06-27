<?php

namespace MVC;

class Router
{

    public $rutasGET = [];
    public $rutasPOST = [];

    public function get($url, $fn)
    {
        $this->rutasGET[$url] = $fn;
    }

    public function post($url, $fn)
    {
        $this->rutasPOST[$url] = $fn;
    }

    public function comprobarRutas()
    {

        session_start();
        $auth = $_SESSION['login'] ?? null;

        //Array de rutas protegidas
        $tutas_protegidas = ['/admin', '/propiedades/crear', '/propiedades/actualizar', '/propiedades/eliminar', '/vendedores/crear', '/vendedores/actualizar', '/vendedores/eliminar'];


        $urlActual = $_SERVER['REQUEST_URI'] ?? '/';
        $metodo = $_SERVER['REQUEST_METHOD'];

        if ($metodo === 'GET') {
            $fn = $this->rutasGET[$urlActual] ?? null;
        } else {
            $fn = $this->rutasPOST[$urlActual] ?? null;
        }

        //Proteger las rutas
        if (in_array($urlActual, $tutas_protegidas) && !$auth) {
            header('Location: /');
        }

        if ($fn) {
            //La URL existe y hay una funcion asociada
            call_user_func($fn, $this); //Permite llamar una funcion cuando no sabemos como se llama

        } else {
            //La URL no existe. 404
            echo '404';
        }
    }

    //Muestra una vista
    public function render($view, $datos=[]) {

        foreach ($datos as $key => $value) {
            $$key = $value; //Variable de variable
        }

        ob_start(); //Almacenamiento en memoria durante un momento..

        include __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); //Limpia el buffer
        include __DIR__ . "/views/layout.php";
    }
}
