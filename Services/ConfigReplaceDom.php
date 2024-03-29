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

use Austral\SeoBundle\Entity\Interfaces\UrlParameterInterface;
use Austral\SeoBundle\Routing\AustralRouting;
use Austral\SeoBundle\Services\UrlParameterManagement;
use Austral\HttpBundle\Services\DomainsManagement;
use Austral\ToolsBundle\AustralTools;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

/**
 * Austral ConfigReplaceDom Bundle.
 * @author Matthieu Beurel <matthieu@austral.dev>
 */
Class ConfigReplaceDom
{

  /**
   * @var ConfigVariable
   */
  protected ConfigVariable $configVariables;

  /**
   * @var DomainsManagement
   */
  protected DomainsManagement $domainsManagement;

  /**
   * @var UrlParameterManagement
   */
  protected UrlParameterManagement $urlParameterManagement;

  /**
   * @var AustralRouting
   */
  protected AustralRouting $australRouting;

  /**
   * @param DomainsManagement $domainsManagement
   * @param ConfigVariable $configVariable
   * @param UrlParameterManagement $urlParameterManagement
   * @param AustralRouting $australRouting
   */
  public function __construct(ConfigVariable $configVariable, DomainsManagement $domainsManagement, UrlParameterManagement $urlParameterManagement, AustralRouting $australRouting)
  {
    $this->domainsManagement = $domainsManagement;
    $this->configVariables = $configVariable;
    $this->urlParameterManagement = $urlParameterManagement;
    $this->australRouting = $australRouting;
  }

  /**
   * @var array|string[]
   */
  protected array $formatByTexte = array(
    "year"    =>  "Y",
    "month"   =>  "m",
    "day"     =>  "d"
  );

  /**
   * @param string $dom
   * @param int $referenceType
   *
   * @return string
   * @throws \Exception
   */
  public function replaceDom(string $dom, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
  {
    $dom = $this->replaceByConfig($dom);
    $dom = $this->replaceDateNow($dom);
    return $this->replaceInternalLinks($dom, $referenceType);
  }

  /**
   * @param string $dom
   *
   * @return string
   */
  protected function replaceDateNow(string $dom): string
  {
    preg_match_all('|(%dateNow.*%)|iuU', $dom, $matchs);
    $matchContentValues = AustralTools::getValueByKey($matchs, 1, array());
    $values = array();
    if(count($matchContentValues))
    {
      foreach($matchContentValues as $matchContentValue)
      {
        $values[$matchContentValue] = $matchContentValue;
      }
    }
    if($values)
    {
      $dateNow = new \DateTime();
      $replaceValues = array();
      foreach($values as $value)
      {
        $formatTexte = str_replace(array("%", "dateNow."), "", $value);
        if($format = AustralTools::getValueByKey($this->formatByTexte, $formatTexte, null))
        {
          $replaceValues[$value] = $dateNow->format($format);
        }
      }
      if(count($replaceValues))
      {
        $dom = str_replace($values, $replaceValues, $dom);
      }
    }
    return $dom;
  }

  /**
   * @param string $dom
   *
   * @return string
   * @throws \Exception
   */
  protected function replaceByConfig(string $dom): string
  {
    $dom = preg_replace('/<span style="color: inherit;" data-tag-value="%(.*)%">(.*)<\/span>/', "<span class='config-element element_$1'>$2</span>", $dom);
    preg_match_all('|\[\[(.*)\]\]|iuU', $dom, $matchs);
    $matchContentValues = AustralTools::getValueByKey($matchs, 1, array());
    if(count($matchContentValues))
    {
      foreach($matchContentValues as $matchContentValue)
      {
        $values = json_decode(html_entity_decode($matchContentValue), true);
        if(is_array($values) && array_key_exists("value", $values))
        {
          $dom = str_replace("[[{$matchContentValue}]]", "%{$values['value']}%", $dom);
        }
      }
    }
    preg_match_all('|(%[a-zA-Z-.-_]+%)|iuU', $dom, $matchs);
    $matchContentValues = AustralTools::getValueByKey($matchs, 1, array());
    $values = array();
    if(count($matchContentValues))
    {
      foreach($matchContentValues as $matchContentValue)
      {
        $values[$matchContentValue] = $matchContentValue;
      }
    }
    if($values)
    {
      $variables = $this->configVariables->selectVariableForReplace();
      $replaceValues = array();

      foreach($values as $key => $value)
      {
        $keyObject = str_replace(array("%"), "", $value);
        $noSpan = true;
        $nl2br = false;
        if(strpos($value, "_span") !== false)
        {
          $noSpan = false;
          $keyObject = str_replace("_span", "", $keyObject);
        }
        if(strpos($value, "_nl2br") !== false)
        {
          $nl2br = true;
          $keyObject = str_replace("_nl2br", "", $keyObject);
        }

        if(array_key_exists($keyObject, $variables))
        {
          $variableReplace = AustralTools::getValueByKey($variables, $keyObject, "");
          $variableReplace = $nl2br ? nl2br($variableReplace) : $variableReplace;
          $replaceValues[$value] = $noSpan ? $variableReplace : sprintf("<span class='config-element element_%s'>%s</span>", AustralTools::strip($keyObject), $variableReplace);
        }
        else
        {
          unset($values[$key]);
        }
      }
      if(count($replaceValues))
      {
        $dom = str_replace($values, $replaceValues, $dom);
      }
    }
    return $dom;
  }

  /**
   * @param string $dom
   * @param int $referenceType
   *
   * @return string
   */
  protected function replaceInternalLinks(string $dom, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
  {
    preg_match_all('/((#|&#x23;)INTERNAL_LINK_.*(#|&#x23;))/iuU', $dom, $matchs);
    $matchContentValues = AustralTools::getValueByKey($matchs, 1, array());
    $values = array();
    if(count($matchContentValues))
    {
      foreach($matchContentValues as $matchContentValue)
      {
        $values[$matchContentValue] = $matchContentValue;
      }
    }

    if($values)
    {
      $replaceValues = array();
      foreach($values as $key => $value)
      {
        $linkKeyAndId = str_replace(array("&#x23;","#", "INTERNAL_LINK_"), "", $value);
        if(strpos($linkKeyAndId, "_") !== false || strpos($linkKeyAndId, ":") !== false)
        {
          list($linkKey, $id) = $this->decodeInternalLink($linkKeyAndId);

          /** @var UrlParameterInterface $urlParameter */
          if($urlParameter = $this->urlParameterManagement->getUrlParameterByObjectClassnameAndId($linkKey, $id))
          {
            if(!$urlParameter->getPathLast())
            {
              $path = $this->australRouting->generate("austral_website_homepage", $urlParameter, array(), $urlParameter->getDomainId(), $referenceType);
            }
            else
            {
              $path = $this->australRouting->generate("austral_website_page", $urlParameter, array(), $urlParameter->getDomainId(), $referenceType);
            }
            $addScheme = false;
            $currentDomain = $this->domainsManagement->getCurrentDomain();
            if($currentDomain->getIsTranslate())
            {
              $currentDomain = $currentDomain->getMaster();
            }
            if($currentDomain->getId() !== $urlParameter->getDomainId())
            {
              $addScheme = true;
            }
            if($addScheme)
            {
              $path = "//{$urlParameter->getDomain()->getDomain()}{$path}";
            }

            $replaceValues[$value] = $path;
          }

          if(!array_key_exists($value, $replaceValues))
          {
            unset($values[$key]);
          }
        }
        else
        {
          unset($values[$key]);
        }
      }
      if(count($replaceValues))
      {
        $dom = str_replace(array_keys($replaceValues), array_values($replaceValues), $dom);
      }
    }
    return $dom;
  }

  /**
   * @param $linkKeyAndId
   *
   * @return array
   */
  protected function decodeInternalLink($linkKeyAndId): array
  {
    if(strpos($linkKeyAndId, "::") !== false)
    {
      return explode("::", $linkKeyAndId);
    }
    elseif(strpos($linkKeyAndId, "_") !== false)
    {
      return explode("_", $linkKeyAndId);
    }
    else
    {
      return explode(":", $linkKeyAndId);
    }
  }


}