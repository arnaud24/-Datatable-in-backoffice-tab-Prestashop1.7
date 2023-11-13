<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class InfosListeNaissance extends Module
{
    public function __construct()
    {
        $this->name = 'infoslistenaissance';
        $this->tab = 'administration';
        $this->version = '1.0.1';
        $this->author = 'Arnaud Friconnet';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Tableau Listes de Naissance');
        $this->description = $this->l('Affiche un tableau des listes de naissance dans le backoffice.');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        $this->bootstrap = true;
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('displayBackOfficeHeader') ||
            !$this->installTab()
        ) {
            return false;
        }
        return true;
    }
    

    public function uninstall()
    {
        if (!parent::uninstall() || !$this->uninstallTab()) {
            return false;
        }
        return true;
    }
    
    public function enable($force_all = false)
    {
        return parent::enable($force_all) && $this->installTab();
    }

    public function disable($force_all = false)
    {
        return parent::disable($force_all) && $this->uninstallTab();
    }

    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminInfosListeNaissance');
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'AdminInfosListeNaissance';
        $tab->name = array();
    
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('Listes de Naissance', array(), 'Modules.InfosListeNaissance.Admin', $lang['locale']);
        }
    
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentModulesSf');
        $tab->module = $this->name;
    
        return $tab->save();
    }
    
    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('AdminInfosListeNaissance');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }

    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addJS('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js');
        $this->context->controller->addJS('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js');
        $this->context->controller->addJS('https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-1.13.6/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/kt-2.10.0/r-2.5.0/sc-2.2.0/sb-1.6.0/sp-2.2.0/datatables.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/datatablecustom.min.js');
        $this->context->controller->addCSS('https://cdn.datatables.net/v/dt/jszip-3.10.1/dt-1.13.6/b-2.4.2/b-colvis-2.4.2/b-html5-2.4.2/b-print-2.4.2/date-1.5.1/kt-2.10.0/r-2.5.0/sc-2.2.0/sb-1.6.0/sp-2.2.0/datatables.min.css');
    }

    public function getPublicListeNaissanceData()
    {
        return $this->getListeNaissanceData();
    }

    public function getModulePath()
    {
        return $this->_path;
    }

    private function getListeNaissanceData()
    {
        try {
            $sql = "SELECT
                wp.id_wishlist_product AS 'id_liste_naissance',
                wp.id_customer AS 'id_client',
                c.email AS 'email_client',
                c.firstname AS 'prenom_client',
                c.lastname AS 'nom_client',
                pl.name AS 'nom_produit',
                GROUP_CONCAT(DISTINCT al.name ORDER BY pac.id_product_attribute) AS 'declinaison_produit'
            FROM
                " . _DB_PREFIX_ . "nxtal_wishlist_product wp
            INNER JOIN
                " . _DB_PREFIX_ . "customer c ON wp.id_customer = c.id_customer
            INNER JOIN
                " . _DB_PREFIX_ . "product_lang pl ON wp.id_product = pl.id_product
                LEFT JOIN
                    " . _DB_PREFIX_ . "product_attribute_combination pac ON wp.id_product_attribute = pac.id_product_attribute
                LEFT JOIN
                    " . _DB_PREFIX_ . "attribute_lang al ON pac.id_attribute = al.id_attribute
                GROUP BY
                    wp.id_wishlist_product
                ORDER BY
                    wp.id_wishlist_product DESC";
    
                $result = Db::getInstance()->executeS($sql);
    
                $listenaissanceData = array();
                foreach ($result as $row) {
                    $listenaissanceData[] = array(
                        'id_liste_naissance' => $row['id_liste_naissance'],
                        'id_client' => $row['id_client'],
                        'email_client' => $row['email_client'],
                        'prenom_client' => $row['prenom_client'],
                        'nom_client' => $row['nom_client'],
                        'nom_produit' => $row['nom_produit'],
                        'declinaison_produit' => $row['declinaison_produit'],
                    );
                }
    
                return $listenaissanceData;
            } catch (Exception $e) {
                // Gérer les erreurs de base de données ici si nécessaire
                error_log("Erreur de base de données : " . $e->getMessage());
                return array(); // Retourner une valeur par défaut en cas d'erreur
            }
    }
    
    
}
