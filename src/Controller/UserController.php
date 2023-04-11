<?php

// src/Controller/UserController.php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /*public function list(Request $request, EntityManagerInterface $entityManager)
    {
        $content = json_decode($request->getContent(), true);

        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->json([
            'success' => true,
            'data' => [
                'users' => $users,
            ],
        ]);
    }  */
    public function list(Request $request, EntityManagerInterface $entityManager)
    {
        $content = json_decode($request->getContent(), true);
    
        $qb = $entityManager->createQueryBuilder();
        $qb->select('u')
        ->from(User::class, 'u');

        // Agregar condiciones según los filtros recibidos
        // TODO: Separar a service todo aquí por más legible
        if (isset($content['filters'])) {
            $conditions = array();
        
            foreach ($content['filters'] as $filter) {
                $value = $filter['value'];
                switch ($filter['operator']) {
                    case 'equals':
                        $conditions[] = $qb->expr()->eq("u.{$filter['field']}", ":{$filter['field']}");
                        $qb->setParameter($filter['field'], $value);
                        break;
                    case 'not_equals':
                        $conditions[] = $qb->expr()->neq("u.{$filter['field']}", ":{$filter['field']}");
                        $qb->setParameter($filter['field'], $value);
                        break;
                    case 'greater_than':
                        $conditions[] = $qb->expr()->gt("u.{$filter['field']}", ":{$filter['field']}");
                        $qb->setParameter($filter['field'], $value);
                        break;
                    case 'less_than':
                        $conditions[] = $qb->expr()->lt("u.{$filter['field']}", ":{$filter['field']}");
                        $qb->setParameter($filter['field'], $value);
                        break;
                    case 'between':
                        $conditions[] = $qb->expr()->between("u.{$filter['field']}", ":min", ":max");
                        $qb->setParameter('min', $value['min'])
                           ->setParameter('max', $value['max']);
                        break;
                }
            }
            // TODO: Separar a service todo aquí por más legible
        
            $qb->andWhere(implode(' AND ', $conditions));
        }
        
        $users = $qb->getQuery()->getResult();

        return $this->json([
            'success' => true,
            'data' => [
                'users' => $users,
            ],
        ]);
    }      

    public function show(EntityManagerInterface $entityManager, int $id)
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        return $this->json([
            'success' => true,
            'data' => [
                'user' => $user,
            ],
        ]);
    }

    public function new(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager)
    {
        $content = json_decode($request->getContent(), true);

        // TODO: Separar validaciones a service todo aquí por más legible
        $validator = Validation::createValidator();
        $constraints = new Collection([
            'nombre' => new NotBlank(),
            'apellidos' => new Optional(),
            'poblacion' => new NotBlank(),
            'categoria' => [
                new Optional([
                    new Choice(['choices' => ['x', 'y', 'z']])
                ])
            ],
            'edad' => new NotBlank(),
            'activo' => new NotBlank()
        ]);
        // TODO: Separar validaciones a service
        
        $violations = $validator->validate($content, $constraints);
        
        if (count($violations) > 0) {
            return $this->json([
                'success' => false,
                'data' => "Datos no enviados correctamente",
            ]);
        }

        $user = new User();
        $user->setNombre($content["nombre"]);
        $user->setApellidos($content["apellidos"]);
        $user->setPoblacion($content["poblacion"]);
        $user->setCategoria($content["categoria"]);
        $user->setEdad($content["edad"]);
        $user->setActivo($content["activo"]);
        $user->setCreatedAt();
        
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->json([
            'success' => true,
            'data' => "user saved",
        ]);

    }

    public function update(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager, int $id)
    {
        $content = json_decode($request->getContent(), true);

        // TODO: Separar validaciones a service todo aquí por más legible
        $validator = Validation::createValidator();
        $constraints = new Collection([
            'nombre' => new Optional(),
            'apellidos' => new Optional(),
            'poblacion' => new Optional(),
            'categoria' => [
                new Optional([
                    new Choice(['choices' => ['x', 'y', 'z']])
                ])
            ],
            'edad' => new Optional(),
            'activo' => new Optional()
        ]);
        // TODO: Separar validaciones a service
        
        $violations = $validator->validate($content, $constraints);
        
        if (count($violations) > 0) {
            return $this->json([
                'success' => false,
                'data' => "Datos no enviados correctamente",
            ]);
        }

        $user = $entityManager->getRepository(User::class)->find($id);
        if(isset($content["nombre"])) $user->setNombre($content["nombre"]);
        if(isset($content["apellidos"])) $user->setApellidos($content["apellidos"]);
        if(isset($content["poblacion"])) $user->setPoblacion($content["poblacion"]);
        if(isset($content["categoria"])) $user->setCategoria($content["categoria"]);
        if(isset($content["edad"])) $user->setEdad($content["edad"]);
        if(isset($content["activo"])) $user->setActivo($content["activo"]);
        $user->setUpdatedAt();
        
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => "user updated",
        ]);
    }

    public function delete(EntityManagerInterface $entityManager, int $id)
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'data' => "user removed",
        ]);
    }
}