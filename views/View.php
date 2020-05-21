<?php

    Class View
    {
        private $_file;
        private $_t;

        public function __construct($action)
        {
            $this->_file = 'views/view' . $action . '.php';
        }

        /*
            Genere et affiche
            la vue 
        */

        public function generate($data)
        {
            // partie specifique de la vue;
            $content = $this->generateFile($this->_file, $data);
            // template = header + footer
            $view = $this->generateFile('views/template.php', array('t' => $this->_t, 'content' => $content));

            echo $view;
        }

        /*
            Genere le fichier vue
            et envoie le resultat
            produit
        */

        private function generateFile($file, $data)
        {
            if(file_exists($file))
            {
                extract($data);

                ob_start();

                //inclue le fichier vue
                require_once($file);

                return ob_get_clean();
            }
            else
            {
                throw new Exception('Fichier ' . $file . 'Introuvable');
            }
        }
    }

?>
