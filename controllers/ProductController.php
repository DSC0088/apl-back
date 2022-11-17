<?php
namespace app\controllers;

use Yii;

use yii\web\Controller;
use yii\db\Query;

class ProductController extends Controller {
	
	public function actionMain() {
		echo 'apl-test backend';
		exit;
	}

    public static function allowedDomains(){
        return [            
            'http://localhost:8080',            
        ];
    }  
    
    public function behaviors(){
        return array_merge(parent::behaviors(), [            
            'corsFilter'  => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [                    
                    'Origin'                           => static::allowedDomains(),
                    'Access-Control-Request-Method'    => ['GET', 'POST', 'PATCH'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age'           => 3600,
                ],
            ],
        ]);
    }
            
    public function actionGetall() {
        $query = new Query();
        $rows = $query->select('*')->from('product')->all();

        //$rows = [];
        return $this->asJson($rows);
    }

    public function actionGetbyid($id) {

        $query = new Query();
        $row = $query->select('*')->from('product')->where(['id' => $id])->one();

        return $this->asJson($row);
    }

    public function actionGetbybrand($name) {

        $query = new Query();
        $rowmin = $query->select('*')->from('product')->where(['brand_name' => $name])->orderBy('price')->one();
        $rowmax = $query->select('*')->from('product')->where(['brand_name' => $name])->orderBy('price DESC')->one();
        
        return $this->asJson([
            'min' => $rowmin,
            'max' => $rowmax,            
        ]);
    }

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionCreate() {       
        $product = json_decode($_POST['product_json'], true);
        unset($product['id']);

        $query = new Query();        
        $query->createCommand()->insert('product',$product)->execute();        
        $id = Yii::$app->db->getLastInsertID();       
        
        return $this->asJson(['id' => $id]);
    }

    public function actionUpdate($id) {
        $product = json_decode($_POST['product_json'], true);
        $id = intval($product['id']);
        unset($product['id']);

        $query = new Query();        
        $query->createCommand()->update('product',$product, ['id' => $id])->execute();

        $x = ['id' => 7];
        return $this->asJson($x);        
    }
    
}