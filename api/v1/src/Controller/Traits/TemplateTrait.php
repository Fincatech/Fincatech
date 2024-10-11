<?php
/**
* Autor: Oscar R. ( 2021 )
* Descripción: Trait para la gestión de servicios FTP
*
*
**/
namespace HappySoftware\Controller\Traits;

use Exception;

use function PHPUnit\Framework\throwException;

trait TemplateTrait{

    /**
     * Path de templates
     * @param bool $isEmailTemplate. Indica si se desea recuperar el template de un e-mail
     */
    public static function GetTemplatePath(bool $isEmailTemplate = false)
    {
        global $appSettings;
        $templatePath = ABSPATH . ($isEmailTemplate ? $appSettings['storage']['emailtemplates'] : $appSettings['storage']['templates']);
        return $templatePath;
    }

    /**
     * Obtiene el template deseado antes de ser parseado
     * @param string $templateName Nombre del template sin extensión
     * @return string|bool Contenido del template si lo encuentra. False si no existe
     */
    public static function GetTemplate(string $templateName)
    {

        $templatePath = self::GetTemplatePath() . $templateName;
        if(!file_exists($templatePath)){
            throw new Exception('Template no encontrado');
        }

        return file_get_contents($templatePath);

    }

    /**
     * Obtiene el template deseado antes de ser parseado
     * @param string $templateName Nombre del template sin extensión
     * @return string|bool Contenido del template si lo encuentra. False si no existe
     */
    public static function GetEmailTemplate(string $templateName)
    {

        $templatePath = self::GetTemplatePath(true) . $templateName;
        if(!file_exists($templatePath)){
            return false;
        }

        return file_get_contents($templatePath);

    }
    /**
     * Obtiene el template deseado ya parseado con los datos pasados por parámetro
     * @param string $templateName Nombre del template sin extensión
     * @param array $data Datos que van a ser parseados.
     * @return string|bool Contenido del template si lo encuentra. False si no existe
     */
    public static function GetTemplateWithData(string $templateName, array $data)
    {

        $templatePath = self::GetTemplatePath() . $templateName;
        if(!file_exists($templatePath)){
            return false;
        }

        ob_start();
            include_once($templatePath);
            $htmlOutput = ob_get_contents();
        ob_end_clean();
        return $htmlOutput;
        
    }

}