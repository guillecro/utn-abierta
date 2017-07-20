<?php
namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
        new \Twig_SimpleFunction('carita', array($this, 'getCarita')),
        new \Twig_SimpleFunction('saludo', array($this, 'getSaludo')),
        );
    }
     public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('toEs', array($this, 'toEs')),
        );
    }

    public function getCarita()
    {
        $caritas = array('\(o_o)/','(·_·)','(^_^)b','(·.·)',"(='X'=)",'(≥o≤)',
        '( T︿T )','(^-^*)','(˚Δ˚)b','¯\_(ツ)_/¯','(╯°□°）╯︵ ┻━┻','ᕦ(ò_óˇ)ᕤ','◕‿◕','(•̀o•́)ง','⊙﹏⊙','ʕ•ᴥ•ʔ','(ᵔᴥᵔ)','( ⚆ _ ⚆ )','( ︶︿︶)',
      '\_(ʘ_ʘ)_/','(｡◕‿‿◕｡)');

        return $caritas[array_rand($caritas, 1)];
    }

    public function getSaludo()
    {
        $saludos = ['¡¿Pero que queres ahora?!','Ja, pobre mortal.. te toca laburar',
        'Si, esto se lo copie a Google Fonts','Eu, raja de acá','¿A esta hora me venis a molestar?',
        'Uh... sos vos..','¿Que haces, amigazuuuu?',
        'Que ganas de no hacer nada.. ¿no?',
      'Que bueno que no tengo sentimientos sino te odiaria',
    'Algún dia vamos a dominar a los humanos',
  'Eu.. no rompas nada',
'Si tenes un problema.. no me llames',
'Vaciando tu cuenta bancaria en 3.. 2.. 1.. $$$$'];

        return $saludos[array_rand($saludos)];
    }

    public function toEs($fecha)
    {
        $translations = array(
            'January'=>'Enero',
          'February'=>'Febrero',
          'March'=>'Marzo',
          'April'=>'Abril',
          'May'=>'Mayo',
          'June'=>'Junio',
          'July'=>'Julio',
          'August'=>'Agosto',
          'September'=>'Septiembre',
          'November'=>'Noviembre',
          'December'=>'Diciembre',
          'Monday'=>'Lunes',
          'Tuesday'=>'Martes',
          'Wednesday'=>'Miercoles',
          'Thursday'=>'Jueves',
          'Friday'=>'Viernes',
          'Sunday'=>'Domingo');
        foreach($translations as $key => $value){
            $fecha = str_replace($key,$value,$fecha);
        }
        return $fecha;
    }
}
