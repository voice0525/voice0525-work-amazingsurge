<?php namespace Amazingsurge\Products\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use Amazingsurge\Products\Models\Product;

/**
 * Products Back-end Controller
 */
class Products extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Amazingsurge.Products', 'products', 'products');
    }

    public function onDelete()
    {
        if (($productIds = post('checked')) && is_array($productIds) && count($productIds)) {

            foreach ($productIds as $productId) {
                if (!$product = Product::find($productId))
                    continue;

                $product->delete();
            }

            Flash::success('Successfully deleted those products.');
        }

        return $this->listRefresh();
    }
}