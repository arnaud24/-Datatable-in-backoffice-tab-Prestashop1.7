<?php

class AdminInfosListeNaissanceController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';

        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        // Débogage : Vérifier si nous arrivons ici
        //die('initContent called');

        $this->title = $this->l('Listes de Naissance', 'AdminInfosListeNaissanceController');

        // Débogage : Vérifier si le titre est correct
        //die('Title set');

        $listenaissance_data = $this->module->getPublicListeNaissanceData();

        // Débogage : Vérifier si les données sont récupérées correctement
        //die(var_dump($listenaissance_data));

        $this->context->smarty->assign([
            'listenaissance_data' => $listenaissance_data,
        ]);

        // Générez le tableau DataTables ici
        $tableHTML = $this->generateTable();

        // Débogage : Vérifier si le tableau HTML est généré correctement
        //die($tableHTML);

        $content = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'infoslistenaissance/views/templates/admin/infoslistenaissance.tpl');
        $this->context->smarty->assign(array(
            'content' => $this->content . $content,
       ));

        // Débogage : Vérifier le fichier tpl
        //die($this->context->smarty->fetch(_PS_MODULE_DIR_ . 'infoslistenaissance/views/templates/admin/infoslistenaissance.tpl'));
    }


    private function generateTable()
    {
        $listenaissance_data = $this->context->smarty->getTemplateVars('listenaissance_data');

        // Débogage : Vérifier si les données sont disponibles
        //die(var_dump($listenaissance_data));

        $tableHTML = '<table id="listenaissancetable" class="table table-bordered">';
        // En-tête du tableau
        $tableHTML .= '<thead>';
        $tableHTML .= '<tr>';
        $tableHTML .= '<th>ID: LISTE DE NAISSANCE</th>';
        $tableHTML .= '<th>ID: CLIENT</th>';
        $tableHTML .= '<th>EMAIL</th>';
        $tableHTML .= '<th>PRENOM</th>';
        $tableHTML .= '<th>NOM</th>';
        $tableHTML .= '<th>PRODUIT</th>';
        $tableHTML .= '<th>DECLINAISON</th>';
        $tableHTML .= '</tr>';
        $tableHTML .= '</thead>';

        // Corps du tableau
        $tableHTML .= '<tbody>';
        foreach ($listenaissance_data as $listenaissance) {
            $tableHTML .= '<tr>';
            $tableHTML .= '<td>' . $listenaissance['id_liste_naissance'] . '</td>';
            $tableHTML .= '<td>' . $listenaissance['id_client'] . '</td>';
            $tableHTML .= '<td>' . $listenaissance['email_client'] . '</td>';
            $tableHTML .= '<td>' . $listenaissance['prenom_client'] . '</td>';
            $tableHTML .= '<td>' . $listenaissance['nom_client'] . '</td>';
            $tableHTML .= '<td>' . $listenaissance['nom_produit'] . '</td>';
            $tableHTML .= '<td>' . $listenaissance['declinaison_produit'] . '</td>';
            $tableHTML .= '</tr>';
        }
        $tableHTML .= '</tbody>';

        $tableHTML .= '</table>';

        
        // Débogage : Vérifier si le tableau HTML est correct
        //die($tableHTML);

        return $tableHTML;
    }
}

