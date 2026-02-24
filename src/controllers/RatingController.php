<?php
require_once 'Controller.php';
require_once 'src/models/RatingModel.php';
require_once 'src/views/RatingView.php';

class RatingController extends Controller
{
    private $ratingModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->ratingModel = new RatingModel($db);
    }

    //legt neues Rating mit Daten aus request-body in der Datenbank an
    public function store()
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('rating.create');

        //lädt Daten aus request
        $productId = $this->getBody('product_id');
        $score     = $this->getBody('rating_stars');
        $comment   = $this->getBody('rating_comment');
        $userId = $this->currentUser['user_id'];

        if ($productId && $score) {
            $data = [
                'user_id' => $userId,
                'rating_score' => $score,
                'comment' => $comment
            ];

            $this->ratingModel->create($productId, $data);
        }

        $this->redirect(BASE_URL . '/product/show/' . $productId);
    }

    //entfernt Rating aus der Datenbank
    public function remove()
    {
        $this->requireLogin();
        $this->denyAccessUnlessGranted('rating.delete');

        $ratingId = $this->getBody('rating_id');
        $productId = $this->getBody('product_id');
        if($ratingId){
            $this->ratingModel->remove($ratingId);
        }
        $this->redirect(BASE_URL . '/product/show/' . $productId);
    }
}