<?php
/*
 * This file is part of the Austral Website Bundle package.
 *
 * (c) Austral <support@austral.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Austral\WebsiteBundle\Services;
use Austral\EntityFileBundle\File\Link\Generator;
use Austral\EntityBundle\Entity\Interfaces\TranslateMasterInterface;
use Austral\ToolsBundle\AustralTools;
use Austral\WebsiteBundle\Entity\Interfaces\ConfigInterface;
use Austral\WebsiteBundle\Entity\Interfaces\ConfigTranslateInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManager;

/**
 * Austral ReplaceConfigValues Bundle.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
Class ConfigVariable
{

  /**
   * @var Generator
   */
  protected Generator $fileLinkGenerator;

  /**
   * @var EntityManager
   */
  protected EntityManager $entityManager;

  /**
   * @var string|null
   */
  protected ?string $language;

  /**
   * @var array
   */
  protected array $variables = array();

  /**
   * ReplaceConfigValues constructor.
   *
   * @param RequestStack $requestStack
   * @param EntityManager $entityManager
   * @param Generator $fileLinkGenerator
   */
  public function __construct(RequestStack $requestStack, EntityManager $entityManager, Generator $fileLinkGenerator)
  {
    $this->fileLinkGenerator = $fileLinkGenerator;
    $request = $requestStack->getCurrentRequest();
    $this->language = $request ? $request->getLocale() : null;
    $this->entityManager = $entityManager;
  }

  /**
   * @param string|null $language
   *
   * @return $this
   */
  public function setLanguage(?string $language): ConfigVariable
  {
    $this->language = $language;
    return $this;
  }

  /**
   * @return array
   * @throws Exception
   */
  final protected function getVariables(): array
  {
    if(!$this->variables)
    {
      $variables = array();
      $configsAll = $this->entityManager->getRepository("Austral\WebsiteBundle\Entity\Interfaces\ConfigInterface")->selectAllByIndexKeyname($this->language);

      /** @var ConfigInterface|TranslateMasterInterface $config */
      foreach($configsAll as $config)
      {
        /** @var ConfigTranslateInterface $configTranslate */
        $configTranslate = $config->getTranslateCurrent();

        if($config->getType() == "all")
        {
          $variables["{$config->getKeyname()}.text"] = array(
            "text"  =>  "{$config->__toString()} - Text",
            "type"  =>  "text",
            "key"   =>  "{$config->getKeyname()}.text",
            "value" =>  nl2br($config->getTranslateCurrent()->getContentText())
          );
          $variables["{$config->getKeyname()}.boolean"] = array(
            "text"  =>  "{$config->__toString()} - Boolean",
            "type"  =>  "boolean",
            "key"   =>  "{$config->getKeyname()}.boolean",
            "value" =>  $config->getTranslateCurrent()->getContentBoolean()
          );
          $variables["{$config->getKeyname()}.image"] = array(
            "text"  =>  "{$config->__toString()} - Picture",
            "type"  =>  "image",
            "key"   =>  "{$config->getKeyname()}.image",
            "value" =>  $this->fileLinkGenerator->image($config, "image")
          );
          $variables["{$config->getKeyname()}.file"] = array(
            "text"  =>  "{$config->__toString()} - File",
            "type"  =>  "file",
            "key"   =>  "{$config->getKeyname()}.file",
            "value" =>  $this->fileLinkGenerator->download($config, "file")
          );
          $variables["{$config->getKeyname()}.internal-link"] = array(
            "text"  =>  "{$config->__toString()} - Internal Link",
            "type"  =>  "internal-link",
            "key"   =>  "{$config->getKeyname()}.internal-link",
            "value" =>  $this->fileLinkGenerator->download($config, "internal-link")
          );
        }
        elseif($config->getType() == "text")
        {
          $variables[$config->getKeyname()] = array(
            "text"  =>  $config->__toString(),
            "type"  =>  "text",
            "key"   =>  $config->getKeyname(),
            "value" =>  nl2br($configTranslate->getContentText())
          );
        }
        elseif($config->getType() == "image")
        {
          $variables[$config->getKeyname()] = array(
            "text"  =>  $config->__toString(),
            "type"  =>  "image",
            "key"   =>  "{$config->getKeyname()}",
            "value" =>  $this->fileLinkGenerator->image($config, "image")
          );
        }
        elseif($config->getType() == "image-text")
        {
          $variables["{$config->getKeyname()}.text"] = array(
            "text"  =>  "{$config->__toString()} - Text",
            "type"  =>  "text",
            "key"   =>  "{$config->getKeyname()}.text",
            "value" =>  nl2br($config->getTranslateCurrent()->getContentText())
          );
          $variables["{$config->getKeyname()}.image"] = array(
            "text"  =>  "{$config->__toString()} - Picture",
            "type"  =>  "image",
            "key"   =>  "{$config->getKeyname()}.image",
            "value" =>  $this->fileLinkGenerator->image($config, "image")
          );
        }
        elseif($config->getType() == "file")
        {
          $variables[$config->getKeyname()] = array(
            "text"  =>  "{$config->__toString()}",
            "type"  =>  "file",
            "key"   =>  "{$config->getKeyname()}",
            "value" =>  $this->fileLinkGenerator->download($config, "file")
          );
        }
        elseif($config->getType() == "file-text")
        {
          $variables["{$config->getKeyname()}.text"] = array(
            "text"  =>  "{$config->__toString()} - Text",
            "type"  =>  "text",
            "key"   =>  "{$config->getKeyname()}.text",
            "value" =>  nl2br($config->getTranslateCurrent()->getContentText())
          );
          $variables["{$config->getKeyname()}.file"] = array(
            "text"  =>  "{$config->__toString()} - File",
            "type"  =>  "file",
            "key"   =>  "{$config->getKeyname()}.file",
            "value" =>  $this->fileLinkGenerator->image($config, "file")
          );
        }
        elseif($config->getType() == "checkbox")
        {
          $variables["{$config->getKeyname()}"] = array(
            "text"  =>  $config->__toString(),
            "type"  =>  "boolean",
            "key"   =>  $config->getKeyname(),
            "value" =>  $config->getTranslateCurrent()->getContentBoolean()
          );
        }
        elseif($config->getType() == "internal-link")
        {
          $variables["{$config->getKeyname()}"] = array(
            "text"  =>  $config->__toString(),
            "type"  =>  "internal-link",
            "key"   =>  $config->getKeyname(),
            "value" =>  "#INTERNAL_LINK_{$config->getTranslateCurrent()->getInternalLink()}#"
          );
        }
      }
      $variables = $this->variablesExtends($variables);
      $this->variables = $variables;
    }
    return $this->variables;
  }

  /**
   * @return array
   * @throws Exception
   */
  public function getAllVariables(): array
  {
    return $this->getVariables();
  }

  /**
   * @param $key
   * @param null $default
   *
   * @return array|mixed|string|null
   * @throws Exception
   */
  public function getVariableByKey($key, $default = null)
  {
    return AustralTools::getValueByKey($this->getVariables(), $key, $default);
  }

  /**
   * @param $key
   * @param null $default
   *
   * @return array|mixed|string|null
   * @throws Exception
   */
  public function getValueVariableByKey($key, $default = null)
  {
    return AustralTools::getValueByKey(
      AustralTools::getValueByKey($this->getVariables(), $key, array()),
      "value",
      $default
    );
  }

  /**
   * @param array $variables
   *
   * @return array
   */
  protected function variablesExtends(array $variables): array
  {
    /*
     * Exemple
        $variables[] = array(
          "text"    =>  "Nombre d'abonné",
          "key"   =>  "abonnes.number",
          "value" =>  100
        );
     */
    return $variables;
  }


  /**
   * @return array
   * @throws Exception
   */
  final public function selectVariableForPopup(): array
  {
    $variables = array();
    foreach($this->getVariables() as $variable)
    {
      $variables[] = array(
        "text"    =>  AustralTools::getValueByKey($variable, "text", null),
        "value"   =>  AustralTools::getValueByKey($variable, "key", null),
      );
    }
    return $variables;
  }

  /**
   * @return array
   * @throws Exception
   */
  final public function selectVariableForReplace(): array
  {
    $variables = array();
    foreach($this->getVariables() as $variable)
    {
      $variables[$variable['key']] = $variable['value'];
    }
    foreach($variables as $key => $variable)
    {
      preg_match_all('|%(\S+)%|iuU', $variable, $matchs);
      $matchContentValues = AustralTools::getValueByKey($matchs, 1, array());
      if(count($matchContentValues))
      {
        foreach($matchContentValues as $matchValue)
        {
          if(array_key_exists($matchValue, $variables))
          {
            $variable = str_replace("%{$matchValue}%", $variables[$matchValue], $variable);
          }
        }
        $variables[$key] = $variable;
      }
    }
    return $variables;
  }




}