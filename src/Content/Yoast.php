<?php

declare(strict_types=1);

namespace Strata\Frontend\Content;

class Yoast
{
    protected $title;
    protected $metadescription;
    protected $metakeywords = [];
    protected $twitter;
    protected $opengraph;

    /**
     * @return mixed
     */
    public function getTwitter()
    {
        $metatags = "";
        if (isset($this->twitter["title"]) && strlen($this->twitter["title"]) > 0) {
            $metatags .= "<meta name=\"twitter:title\" content=\"" . $this->twitter["title"] . "\">";
            //$head->addMeta('twitter:title', $title);
        }
        if (isset($this->twitter["description"]) && strlen($this->twitter["description"]) > 0) {
            $metatags .= "<meta name=\"twitter:description\" content=\"" . $this->twitter["description"] . "\">";
        }
        if (isset($this->twitter["image"]) && strlen($this->twitter["image"]) > 0) {
            $metatags .= "<meta name=\"twitter:image\" content=\"" . $this->twitter["image"] . "\">";
        }
        return $metatags;
    }

    /**
     * @param $twitter_title
     * @param $twitter_description
     * @param $twitter_image
     */
    public function setTwitter($twitter_title, $twitter_description, $twitter_image): void
    {
        $this->twitter = [
            "title" => $twitter_title,
            "description" => $twitter_description,
            "image" => $twitter_image
        ];
    }

    /**
     * @return mixed
     */
    public function getOpengraph()
    {
        $metatags = "";
        if (isset($this->opengraph["title"]) && strlen($this->opengraph["title"]) > 0) {
            $metatags .= "<meta property=\"og:title\" content=\"" . $this->opengraph["title"] . "\">";
        }
        if (isset($this->opengraph["description"]) && strlen($this->opengraph["description"]) > 0) {
            $metatags .= "<meta property=\"og:description\" content=\"" . $this->opengraph["description"] . "\">";
        }
        if (isset($this->opengraph["image"]) && strlen($this->opengraph["image"]) > 0) {
            $metatags .= "<meta property=\"og:image\" content=\"" . $this->opengraph["image"] . "\">";
        }
        return $metatags;
    }

    /**
     * @param $opengraph_title
     * @param $opengraph_description
     * @param $opengraph_image
     */
    public function setOpengraph($opengraph_title, $opengraph_description, $opengraph_image): void
    {
        $this->opengraph = [
            "title" => $opengraph_title,
            "description" => $opengraph_description,
            "image" => $opengraph_image
        ];
    }

    /**
     * @return mixed
     */
    public function getTitle(): ?string
    {
        if (isset($this->title)) {
            return $this->title;
        } else {
            return null;
        }
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getMetadescription()
    {
        return strlen($this->metakeywords) > 0 ? "<meta name=\"description\" content=\"" . $this->metadescription . "\">" : "";
    }

    /**
     * @param mixed $metadescription
     */
    public function setMetadescription($metadescription): void
    {
        $this->metadescription = $metadescription;
    }

    /**
     * @return mixed
     */
    public function getMetakeywords()
    {
        return strlen($this->metakeywords) > 0 ? "<meta name=\"keywords\" content=\"" . $this->metakeywords . "\">" : "";
    }

    /**
     * @param mixed $metakeywords
     */
    public function setMetakeywords($metakeywords): void
    {
        $this->metakeywords = $metakeywords;
    }

    public function getAllMetatags()
    {
        $metatags = "";
        $metatags .= $this->getTwitter();
        $metatags .= $this->getOpengraph();
        $metatags .= $this->getMetakeywords();
        $metatags .= $this->getMetadescription();
//        $metatags .= $this->getTitle();
        return $metatags;
    }

    public function __toString()
    {
        return $this->getAllMetatags();
    }
}
