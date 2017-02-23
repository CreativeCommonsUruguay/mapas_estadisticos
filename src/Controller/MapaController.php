<?php

namespace Drupal\mapas_estadisticos\Controller;

use Drupal\Core\Controller\ControllerBase;

class MapaController extends ControllerBase {

  public function content($nombre) {

    $config = \Drupal::service('config.factory')->getEditable('mapas_estadisticos.settings');

    $cota = 0;

// Obtengo colores RGB inicial y final del degradé en hex y los paso a valores decimales
    $colorInicial = $config->get('colorInicial');
    $colorFinal = $config->get('colorFinal');
    list($r1, $g1, $b1) = sscanf($colorInicial, "#%02x%02x%02x");
    list($r2, $g2, $b2) = sscanf($colorFinal, "#%02x%02x%02x");

  switch ($nombre) {
    case 'uno':
      
      $tituloPag = "Porcentaje de mujeres por territorio";
      for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {
        
        $tid = $config->get('taxonomia'.$i);

        $nidsMujeres = \Drupal::entityQuery('node')
          ->condition('field_sexo.entity.tid', '21') // Cuantas mujeres hay en un departamento determinado
          ->execute();

        $nidsDepartamentos = \Drupal::entityQuery('node')
          ->condition('field_lugardenacimiento.entity.tid', $tid)
          ->execute();

        $mujeresXDepartamento = count(array_intersect_key ($nidsMujeres, $nidsDepartamentos));
        $totalXDepartamento = count($nidsDepartamentos);

        $arreglo['#valor'.$i] = $mujeresXDepartamento/$totalXDepartamento*100;

        if ($arreglo['#valor'.$i] > $cota) { // Encuentro el valor mayor
          $cota = $arreglo['#valor'.$i];
        }
      }
/*
$arreglo['#valor1'] = '26.9'; $arreglo['#valor2'] = '18.5'; $arreglo['#valor3'] = '26.6'; $arreglo['#valor4'] = '32.8'; $arreglo['#valor5'] = '25.6'; $arreglo['#valor6'] = '22.7'; $arreglo['#valor7'] = '23.1'; $arreglo['#valor8'] = '20.4'; $arreglo['#valor9'] = '28.7'; $arreglo['#valor10'] = '24.3'; $arreglo['#valor11'] = '24.2'; $arreglo['#valor12'] = '34.9'; $arreglo['#valor13'] = '23.9'; $arreglo['#valor14'] = '24'; $arreglo['#valor15'] = '27'; $arreglo['#valor16'] = '15.8'; $arreglo['#valor17'] = '18.2'; $arreglo['#valor18'] = '15.4'; $arreglo['#valor19'] = '20.3'; $cota = '34.9';
*/

      for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {

        $R= round((-($r1-$r2)/$cota)*$arreglo['#valor'.$i] + $r1);
        $G= round((-($g1-$g2)/$cota)*$arreglo['#valor'.$i] + $g1);
        $B= round((-($b1-$b2)/$cota)*$arreglo['#valor'.$i] + $b1);

        $arreglo['#color'.$i] = sprintf("#%02x%02x%02x", $R, $G, $B);
      }

      break;

    case 'dos':

      $tituloPag = "Mujeres vivas por territorio";
      for ($i = 1; $i <= $config->get('n_entidades_territoriales'); $i++) {

        $query = \Drupal::entityQuery('node')
          ->condition("field_lugardenacimiento.entity.name", "Montevideo");

        $nids = $query->execute();
        $arreglo['#valor'.$i] = count($nids)+15;
      }
      break;

  }

    $arreglo['#title'] = $tituloPag;
    $arreglo['#theme'] = "svg";
    $arreglo['#attached'] = array ('library' => 'mapas_estadisticos/clases-svg');
    return $arreglo;

  }


}
