<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Entity\User;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TodoController
 * @package App\Controller
 * @Route("/api",name="todo_api")
 */
class TodoController extends AbstractController
{
    public $show = [];
    /**
     * @param TodoRepository $todoRepository
     * @Route("/todos", name="todos",methods={"GET"})
     * @return JsonResponse
     */
    public function getTodos(TodoRepository $todoRepository,EntityManagerInterface  $em)
    {
        $red = $this->getUser()->getId();
        $data = $todoRepository->findBy(["user"=>$red]);

        //dd(array_keys($em->getMetadataFactory()->getMetadataFor(Todo::class)->reflFields));
        return $this->response($data);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param TodoRepository $todoRepository
     * @return JsonResponse
     * @throws \Exception
     * @Route("/todos", name="todos_add", methods={"POST"})
     */
    public function addTodo(Request $request,EntityManagerInterface $em,TodoRepository $todoRepository, Security $security)
    {
        try {
            $request = $this->transformJsonBody($request);  //|| !$request->request->get('performance')
            if (!$request || !$request->get('task') || !$request->get("id")) {
                throw new \Exception();
            }
            $todo = new Todo();
            $todo->setPerformance($request->get('performance'));
            $todo->setUser($this->getUser());
            $todo->setId($request->get('id'));
            $todo->setTask($request->get('task'));

            $em->persist($todo);
            $em->flush();
            $data = [
                'status' => 200,
                'success' => "todo added successfully",
            ];
            return $this->response($data);

        }catch (\Exception $e){
            $data = [
                'status' => 422,
                'errors' => "Data no valid",
            ];
            return $this->response($data, 422);
        }

    }

    /**
     * @param EntityManagerInterface $em
     * @param TodoRepository $todoRepository
     * @param $id
     * @return JsonResponse
     * @Route("/todos/{id}", name="todos_delete", methods={"DELETE"})
     */
     public function deleteTodo(EntityManagerInterface $em,TodoRepository $todoRepository, $id){
        $todo=$todoRepository->find($id);
         if (!$todo){
             $data = [
                 'status' => 404,
                 'errors' => "todo not found",
             ];
             return $this->response($data, 404);
         }
         $em->remove($todo);
         $em->flush();
         $data = [
             'status' => 200,
             'errors' => "Post deleted successfully",
         ];
         return $this->response($data);

     }

    /**
     * @param TodoRepository $todoRepository
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @Route("/update/{id}" ,name="update_todo", methods={"PUT"})
     */
     public function updateTodo(TodoRepository $todoRepository, EntityManagerInterface $em,Request $request,$id){
         $update=$todoRepository->find($id);
         if (!$update){
             $data = [
                 'status' => 404,
                 'errors' => "todo not found",
             ];
             return $this->response($data, 404);
         }
         try {
             $request = $this->transformJsonBody($request);
             if(!$request || !$request->get("task") || !is_bool($request->get("performance"))){
                 throw new \Exception();
             }
                 $update->setTask($request->get("task"));
                 $update->setPerformance($request->get("performance"));
                 $em->flush();
                 $data =[
                     "status"=>200,
                     "errors"=>"todo refresh!"
                 ];
                 return $this->response($update);

         }
         catch (\Exception $e){
             $data = [
                 'status' => 422,
                 'errors' => "Data no valid",
             ];
             return $this->response($data, 422);
         }
     }


    /**
     * Returns a JSON response
     *
     * @param array $data
     * @param $status
     * @param array $headers
     * @return JsonResponse
     */
    public function response($data, $status = 200, $headers = [])
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function transformJsonBody(\Symfony\Component\HttpFoundation\Request $request)
    {
        $data = json_decode($request->getContent(), true);


        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

}
