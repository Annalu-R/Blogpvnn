<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../repository/CategoryRepository.php";

$cat = new CategoryController();

class CategoryController{

	function __construct(){
		
        if(isset($_POST["action"])){
			$action = $_POST["action"];
		}else if(isset($_GET["action"])){
			$action = $_GET["action"];
		}

		if(isset($action)){
			$this->callAction($action);
		} else {
			$msg = "Nenhuma acao a ser processada...";
            print_r($msg);
			//include_once "index.php";
		}
	}

    public function callAction(string $functionName = null){

        if (method_exists($this, $functionName)) {
            $this->$functionName();
        
        } else if(method_exists($this, "preventDefault")) {
            $met = "preventDefault";
            $this->$met();
        
        } else {
            throw new BadFunctionCallException("Usecase not exists");
        }
    }

    public function loadView(string $path, array $data = null, string $msg = null){
        $caminho = __DIR__ . "/../views/" . $path;
        // echo("msg=");
        // print_r($msg);
 
        if(file_exists($caminho)){
             require $caminho;
        } else {
            print "Erro ao carregar a view";
        }
    }

    private function create(){
        
        $category = new CategoryModel();
        // $cat->setNome("aaa");
        // $cat->setTelefone("123213");
        // $cat->setEmail("asd@asd");
//print_r ($_POST["nome"]);

        $category->setTipo($_POST["tipo"]);
		$category->setTag($_POST["tag"]);

        $categoryRepository = new CategoryRepository();
        $idCategory = $categoryRepository->create($category);

        if($idCategory){
			$msg = "Registro inserido com sucesso.";
		}else{
			$msg = "Erro ao inserir o registro no banco de dados.";
		}

        $this->findAll($msg);
    }

    private function loadFormNew(){
        $this->loadView("category/formCadastroCategory.php", null,"teste");
    }    

    private function findAll(string $msg = null){
        
        $categoryRepository = new CategoryRepository();

        $category = $categoryRepository->findAll();

        $data['titulo'] = "listar categorias";
        $data['categorias'] = $category;

        $this->loadView("category/Categorylist.php", $data, $msg);
    }

    private function findCategoryById(){
        $idParam = $_GET['idCategory'];

        $categoryRepository = new CategoryRepository();
        $category = $categoryRepository->findCategoryByIdCategory($idParam);

        print "<pre>";
        print_r($category);
        print "</pre>";
    }

    private function deleteCategoryByIdCategory(){
        $idParam = $_GET['idCategory'];
        $categoryRepository = new CategoryRepository();    

        $qt = $categoryRepository->deleteCategoryById($idParam);
        if($qt){
			$msg = "Registro excluído com sucesso.";
		}else{
			$msg = "Erro ao excluir o registro no banco de dados.";
		}
        $this->findAll($msg);
    }

    private function edit(){
        $idParam = $_GET['idCategory'];
        $categoryRepository = new CategoryRepository(); 
        $category = $categoryRepository->findCategoryByIdCategory($idParam);
        $data['category'] = $category;

        $this->loadView("category/formEditaCategory.php", $data);
    }

    private function update(){
        $category = new CategoryModel();

		$category->setIdCategory($_GET["idCategory"]);
		$category->setTag($_POST["tag"]);
		$category->setTipo($_POST["tipo"]);

        $categoryRepository = new CategoryRepository();
        //print_r($cat);
        $atualizou = $categoryRepository->update($category);
        
        if($atualizou){
			$msg = "Registro atualizado com sucesso.";
		}else{
			$msg = "Erro ao atualizar o registro no banco de dados.";
		}
		// include_once "cadastrar.php";

        $this->findAll($msg);        
    }

    private function preventDefault() {
        print "Ação indefinida...";
    }
}
