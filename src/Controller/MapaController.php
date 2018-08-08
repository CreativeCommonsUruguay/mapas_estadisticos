<?php

namespace Drupal\mapas_estadisticos\Controller;

use Drupal\Core\Controller\ControllerBase;

class MapaController extends ControllerBase {

  public function content($nombre) {

    $config = \Drupal::service('config.factory')->getEditable('mapas_estadisticos.settings');

  switch ($nombre) {
    case 'mujeres-por-territorio':

        $tituloPag = "Porcentaje de mujeres por territorio";
        $arreglo['#theme'] = "mapas-svg";
        $nidsMujeres = \Drupal::entityQuery('node')
          ->condition('type', 'autor')
          ->condition('status', 1)
          ->condition('field_sexo.entity.tid', '22') // Cuantas mujeres hay en total
          ->execute();
          

      for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {
        
        $tid = $config->get('taxonomia'.$i);

        $nidsDepartamentos = \Drupal::entityQuery('node')
          ->condition('type', 'autor')
          ->condition('status', 1)
          ->condition('field_lugardenacimiento.entity.tid', $tid) // Cuantas personas hay en un territorio determinado
          ->execute();

        $mujeresXDepartamento = count(array_intersect_key ($nidsMujeres, $nidsDepartamentos)); // Intersecta las mujeres totales y las personas en un territorio
        $totalXDepartamento = count($nidsDepartamentos);

        $arreglo['#valor'.$i] = round($mujeresXDepartamento/$totalXDepartamento*100);
      }

      break;

    case 'mujeres-vivas-por-territorio':

        $tituloPag = "Porcentaje de mujeres vivas por territorio";
        $arreglo['#theme'] = "mapas-svg";
            
        $hace100Anios = date('Y')-100;
        $nidsMujeres = \Drupal::entityQuery('node')
            ->condition('type', 'autor')
            ->condition('status', 1)
            ->condition('field_sexo.entity.tid', '22')
            ->condition('field_ano_de_nacimiento.value', $hace100Anios, '>')
            ->condition('field_ano_de_muerte', 'NULL', 'IS NULL')  // Cuantas mujeres hay en total, que hayan nacido hace menos de 100 años y que no tengan fecha de muerte
            ->execute();
          
          
        for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {
        
            $tid = $config->get('taxonomia'.$i);
        
            $nidsDepartamentos = \Drupal::entityQuery('node')
                ->condition('type', 'autor')
                ->condition('status', 1)
                ->condition('field_lugardenacimiento.entity.tid', $tid) // Cuantas personas hay en un territorio determinado en total, que hayan nacido hace menos de 100 años y que no tengan fecha de muerte
                ->condition('field_ano_de_nacimiento.value', $hace100Anios, '>')
                ->condition('field_ano_de_muerte.value', 'NULL', 'IS NULL')
                ->execute();
          
            $mujeresXDepartamento = count(array_intersect_key ($nidsMujeres, $nidsDepartamentos));
            $totalXDepartamento = count($nidsDepartamentos);

            $arreglo['#valor'.$i] = round($mujeresXDepartamento/$totalXDepartamento*100);
      }


      break;

    case 'autores-por-territorio':
      $tituloPag = "Porcentaje de autores y autoras por territorio";
      $arreglo['#theme'] = "mapas-svg";
      
      $autoresTotales = 0;
      for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {

        $tid = $config->get('taxonomia'.$i);

        $nidsDepartamentos = \Drupal::entityQuery('node')
          ->condition('type', 'autor')
          ->condition('status', 1)
          ->condition('field_lugardenacimiento.entity.tid', $tid) // Cuantas personas hay en un territorio determinado
          ->execute();
          
      $autoresTotales = $autoresTotales+count($nidsDepartamentos);
      }

      for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {

        $tid = $config->get('taxonomia'.$i);

        $nidsDepartamentos = \Drupal::entityQuery('node')
          ->condition('type', 'autor')
          ->condition('status', 1)
          ->condition('field_lugardenacimiento.entity.tid', $tid) // Cuantas personas hay en un territorio determinado
          ->execute();

        $autoresXDepartamento = count($nidsDepartamentos); // Intersecta las mujeres totales y las personas en un territorio

        $arreglo['#valor'.$i] = round($autoresXDepartamento/$autoresTotales*100, 1);
      }


      break;


    case 'autores-vivos-por-territorio':
      $tituloPag = "Porcentaje de autores y autoras vivas por territorio";
      $arreglo['#theme'] = "mapas-svg";
    
      $hace100Anios = date('Y')-100;
      $autoresTotales = 0;
      for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {

        $tid = $config->get('taxonomia'.$i);

        $nidsDepartamentos = \Drupal::entityQuery('node')
          ->condition('type', 'autor')
          ->condition('status', 1)
          ->condition('field_lugardenacimiento.entity.tid', $tid) // Cuantas personas hay en un territorio determinado
          ->condition('field_ano_de_nacimiento.value', $hace100Anios, '>')
          ->condition('field_ano_de_muerte.value', 'NULL', 'IS NULL')
          ->execute();
          
      $autoresTotales = $autoresTotales+count($nidsDepartamentos);
      }

      for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {

        $tid = $config->get('taxonomia'.$i);

        $nidsDepartamentos = \Drupal::entityQuery('node')
          ->condition('type', 'autor')
          ->condition('status', 1)
          ->condition('field_lugardenacimiento.entity.tid', $tid) // Cuantas personas hay en un territorio determinado
          ->condition('field_ano_de_nacimiento.value', $hace100Anios, '>')
          ->condition('field_ano_de_muerte.value', 'NULL', 'IS NULL')
          ->execute();

        $autoresXDepartamento = count($nidsDepartamentos); // Intersecta las mujeres totales y las personas en un territorio

        $arreglo['#valor'.$i] = round($autoresXDepartamento/$autoresTotales*100, 1);
      }
      
      break;

    case 'autores-en-dominio-publico-por-territorio':
      $tituloPag = "Porcentaje de autores y autoras en dominio público por territorio";
      $arreglo['#theme'] = "mapas-svg";
      
            $query = \Drupal::entityQuery('node');
            $group1 = $query->orConditionGroup()
                ->condition('field_estatusderechos.entity.name', '0')
                ->condition('field_estatusderechos.entity.name', '11')
                ->condition('field_estatusderechos.entity.name', '4')
                ->condition('field_estatusderechos.entity.name', '6')
                ->condition('field_estatusderechos.entity.name', '9');
            $idsAutoresenDP = $query
                ->condition('type', 'autor')
                ->condition('status', 1)
                ->condition($group1)
                ->execute();
                
                
      for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {

        $tid = $config->get('taxonomia'.$i);

        $nidsDepartamentos = \Drupal::entityQuery('node')
          ->condition('type', 'autor')
          ->condition('status', 1)
          ->condition('field_lugardenacimiento.entity.tid', $tid) // Cuantas personas hay en un territorio determinado
          ->execute();


        $autoresXDepartamento = count($nidsDepartamentos); // Intersecta las mujeres totales y las personas en un territorio

        $DPXDepartamento = count(array_intersect_key ($idsAutoresenDP, $nidsDepartamentos));
            
        $arreglo['#valor'.$i] = round($DPXDepartamento/$autoresXDepartamento*100, 1);
      }


      break;
      

    case 'estatus-derecho-de-autor':
      $tituloPag = "Porcentaje de estatus de derechos de autores y autoras";
      $arreglo['#theme'] = "pie-svg";
    break;

    case 'autores-por-sexo':
      $tituloPag = "Porcentaje de autores según sexo";
      $arreglo['#theme'] = "pie-svg";
    break;
    
    case 'autores-por-disciplina':
      $tituloPag = "Porcentaje de autores y autoras por disciplina";
      $arreglo['#theme'] = "pie-svg";
    break;
    
    case 'escritores-de-ficcion':
      $tituloPag = "Porcentaje de escritores y escritoras de ficción";
      $arreglo['#theme'] = "pie-svg";
    break;

    case 'escritores-de-no-ficcion':
      $tituloPag = "Porcentaje de escritores y escritoras de no ficción";
      $arreglo['#theme'] = "pie-svg";
    break;

    case 'escritores-de-ficcion-y-no-ficcion':
      $tituloPag = "Porcentaje de escritores y escritoras de ficción y no ficción";
      $arreglo['#theme'] = "pie-svg";
    break;

    case 'artistas-visuales':
      $tituloPag = "Porcentaje de artistas visuales";
      $arreglo['#theme'] = "pie-svg";
    break;

    case 'total-acumulado-de-autores':
      $tituloPag = "Total acumulado de autores";
      $arreglo['#theme'] = "flot-svg";
    break;
    
    case 'nacimientos-de-autores-por-ano':
      $tituloPag = "Nacimientos de autores por año";
      $arreglo['#theme'] = "flot-svg";
    break;


    
   }
  
  if ($arreglo['#theme'] == "mapas-svg") {
// Obtengo colores RGB inicial y final del degradé en hex y los paso a valores decimales
    $cota = 0;
    
    $colorInicial = $config->get('colorInicial');
    $colorFinal = $config->get('colorFinal');
    list($r1, $g1, $b1) = sscanf($colorInicial, "#%02x%02x%02x");
    list($r2, $g2, $b2) = sscanf($colorFinal, "#%02x%02x%02x");
    
    for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) { // Encuentra la cota a los valores
        if ($arreglo['#valor'.$i] > $cota) {
            $cota = $arreglo['#valor'.$i];
        }
    }

    for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) { // Asigna colores en función de los valores

        $R= round((-($r1-$r2)/$cota)*$arreglo['#valor'.$i] + $r1);
        $G= round((-($g1-$g2)/$cota)*$arreglo['#valor'.$i] + $g1);
        $B= round((-($b1-$b2)/$cota)*$arreglo['#valor'.$i] + $b1);

        $arreglo['#color'.$i] = sprintf("#%02x%02x%02x", $R, $G, $B);
    }
    $arreglo['#attached'] = array ('library' => 'mapas_estadisticos/clases-svg');

  }

  if ($arreglo['#theme'] == "pie-svg") {
    $arreglo['#attached'] = array ('library' => 'mapas_estadisticos/clases-pie');
  }

  if ($arreglo['#theme'] == "flot-svg") {
    $arreglo['#attached'] = array ('library' => 'mapas_estadisticos/clases-flot');
  }

    $arreglo['#title'] = $tituloPag;

    return $arreglo;
    
  }


}
