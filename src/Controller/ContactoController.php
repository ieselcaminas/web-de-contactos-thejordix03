<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Contacto;
use App\Entity\Provincia;
use Doctrine\Persistence\ManagerRegistry;


class ContactoController extends AbstractController
{
    private $contactos = [
        1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],
        2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],
        5 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],
        7 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],
        9 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"],
    ];
#[Route('/contacto/{codigo}', name: 'ficha_contacto', requirements: ['codigo' => '\d+'])]
public function ficha(int $codigo, ManagerRegistry $doctrine): Response
{
    $entityManager = $doctrine->getManager();

    // Obtener el repositorio de la entidad Contacto
    $repositorio = $entityManager->getRepository(Contacto::class);

    // Buscar el contacto por id (clave primaria)
    $contacto = $repositorio->find($codigo);

    if ($contacto) {
        return $this->render('ficha_contacto.html.twig', [
            'contacto' => $contacto
        ]);
    }

    return new Response("<html><body>Contacto $codigo no encontrado</body></html>");
}

 
    #[Route('/insertar-contactos', name: 'insertar_contactos')]
public function insertar(ManagerRegistry $doctrine): Response
{
    $entityManager = $doctrine->getManager();

    // Array con los contactos
    $contactos = [
        ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],
        ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],
        ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],
        ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],
        ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"],
    ];

    foreach ($contactos as $dato) {
        $contacto = new Contacto();
        $contacto->setNombre($dato['nombre']);
        $contacto->setTelefono($dato['telefono']);
        $contacto->setEmail($dato['email']);

        $entityManager->persist($contacto);
    }

    $entityManager->flush();

    return new Response('<html><body>Contactos insertados correctamente</body></html>');
}
#[Route('/contacto/actualizar/{id}/{nuevoTelefono}', name: 'actualizar_contacto')]
public function actualizar(int $id, string $nuevoTelefono, ManagerRegistry $doctrine): Response
{
    $em = $doctrine->getManager();
    $repositorio = $em->getRepository(Contacto::class);

    $contacto = $repositorio->find($id);

    if (!$contacto) {
        return new Response("<html><body>Contacto con id $id no encontrado</body></html>");
    }

    // Modificamos un campo, por ejemplo el teléfono
    $contacto->setTelefono($nuevoTelefono);

    // No es necesario persist($contacto) si ya es manejado
    $em->flush();

    return new Response("<html><body>Contacto actualizado: ID $id, nuevo teléfono: $nuevoTelefono</body></html>");
}
#[Route('/contacto/eliminar/{id}', name: 'eliminar_contacto')]
public function eliminar(int $id, ManagerRegistry $doctrine): Response
{
    $em = $doctrine->getManager();
    $repositorio = $em->getRepository(Contacto::class);

    $contacto = $repositorio->find($id);

    if (!$contacto) {
        return new Response("<html><body>❌ Contacto con id $id no encontrado.</body></html>");
    }

    $em->remove($contacto);
    $em->flush();

    return new Response("<html><body>✅ Contacto con id $id eliminado correctamente.</body></html>");
}
#[Route('/contacto/crear', name: 'crear_contacto')]
public function crear(ManagerRegistry $doctrine): Response
{
    $em = $doctrine->getManager();

    // Buscamos si existe una provincia "Valencia"
    $provinciaRepo = $em->getRepository(Provincia::class);
    $provincia = $provinciaRepo->findOneBy(['nombre' => 'Valencia']);

    // Si no existe, la creamos
    if (!$provincia) {
        $provincia = new Provincia();
        $provincia->setNombre('Valencia');
        $em->persist($provincia);
    }

    // Creamos un contacto asignado a esa provincia
    $contacto = new Contacto();
    $contacto->setNombre("Pedro García");
    $contacto->setTelefono("600123456");
    $contacto->setEmail("pedrog@ieselcaminas.org");
    $contacto->setProvincia($provincia);

    // Guardamos en la BD
    $em->persist($contacto);
    $em->flush();

    return new Response("✅ Contacto '{$contacto->getNombre()}' creado en la provincia '{$provincia->getNombre()}'");
}

#[Route('/contacto/ver/{id}', name: 'ver_contacto')]
public function ver(int $id, ManagerRegistry $doctrine): Response
{
    $repositorio = $doctrine->getRepository(Contacto::class);
    $contacto = $repositorio->find($id);

    if (!$contacto) {
        return new Response("❌ Contacto no encontrado");
    }

    $nombreProvincia = $contacto->getProvincia()->getNombre();
    return new Response("El contacto {$contacto->getNombre()} pertenece a la provincia $nombreProvincia");
}
#[Route('/contacto/provincia/{id}', name: 'ver_provincia_contacto')]
public function verProvincia(int $id, ManagerRegistry $doctrine): Response
{
    $repo = $doctrine->getRepository(Contacto::class);
    $contacto = $repo->find($id);

    if (!$contacto) {
        return new Response("❌ Contacto con id $id no encontrado");
    }

    $provincia = $contacto->getProvincia();

    if ($provincia) {
        return new Response("📍 El contacto {$contacto->getNombre()} pertenece a la provincia {$provincia->getNombre()}");
    } else {
        return new Response("ℹ️ El contacto {$contacto->getNombre()} no tiene provincia asignada");
    }
}





}
