<?php

namespace Drupal\blog_outils\Libraries;

class Outils
{
  /**
   * @param $chaine
   * @param $max
   * @return string
   */
  public static function tronque($chaine, $max)
  {
    // Nombre de caractère
    if (strlen($chaine) >= $max)
    {
      // Met la portion de chaine dans $chaine
      $chaine = substr($chaine,0, $max);

      // position du dernier espace
      $dernier_espace = strrpos($chaine," ");

      if ($dernier_espace)
      {
        // Couper encore la chaine
        $chaine = substr($chaine,0, $dernier_espace);
      }

      // Ajoute "..." à la fin
      $chaine .= '...';
    }

    return $chaine;
  }
}
