<?php

declare(strict_types=1);

namespace App\Import\Dto;

use Illuminate\Support\Collection;

class FeedMapTo
{
    private string $feedSlug;

    private string $feedName;

    private string $locale;

    private Collection $categoryMapping;

    private Collection $woocommerceCategories;

    private Collection $woocommerceAttributes;

    private Collection $feedMapping;

    private Collection $rules;

    /**
     * @return string
     */
    public function getFeedSlug(): string
    {
        return $this->feedSlug;
    }

    /**
     * @param  string  $feedSlug
     * @return FeedMapTo
     */
    public function setFeedSlug(string $feedSlug): self
    {
        $this->feedSlug = $feedSlug;

        return $this;
    }

    /**
     * @return string
     */
    public function getFeedName(): string
    {
        return $this->feedName;
    }

    /**
     * @param  string  $feedName
     * @return FeedMapTo
     */
    public function setFeedName(string $feedName): self
    {
        $this->feedName = $feedName;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getCategoryMapping(): Collection
    {
        return $this->categoryMapping;
    }

    /**
     * @param  Collection  $categoryMapping
     * @return FeedMapTo
     */
    public function setCategoryMapping(Collection $categoryMapping): self
    {
        $this->categoryMapping = $categoryMapping;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFeedMapping(): Collection
    {
        return $this->feedMapping;
    }

    /**
     * @param  Collection  $feedMapping
     * @return FeedMapTo
     */
    public function setFeedMapping(Collection $feedMapping): self
    {
        $this->feedMapping = $feedMapping;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    /**
     * @param  Collection  $rules
     * @return FeedMapTo
     */
    public function setRules(Collection $rules): self
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param  string  $locale
     * @return FeedMapTo
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getWoocommerceCategories(): Collection
    {
        return $this->woocommerceCategories;
    }

    /**
     * @param  Collection  $woocommerceCategories
     * @return FeedMapTo
     */
    public function setWoocommerceCategories(Collection $woocommerceCategories): self
    {
        $this->woocommerceCategories = $woocommerceCategories;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getWoocommerceAttributes(): Collection
    {
        return $this->woocommerceAttributes;
    }

    /**
     * @param  Collection  $woocommerceAttributes
     * @return FeedMapTo
     */
    public function setWoocommerceAttributes(Collection $woocommerceAttributes): self
    {
        $this->woocommerceAttributes = $woocommerceAttributes;

        return $this;
    }
}
