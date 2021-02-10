<?php

namespace App\Assistants\Transformers\JsonApi;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelatedLinksContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Contracts\JsonAPIPresentableContract;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class Links
 *
 * @package App\Assistants\Transformers\JsonApi
 */
class Links implements JsonAPIPresentableContract
{
    /**
     * @var Collection
     */
    public Collection $links;

    /**
     * @var LinkParameters
     */
    public LinkParameters $linkParameters;

    /**
     * @param LinkParameters    $linkParameters
     * @param PresenterContract $presenter
     * @param bool              $related
     * @param null|string       $relatedRelationName
     */
    public function __construct(
        LinkParameters $linkParameters,
        PresenterContract $presenter,
        bool $related = false,
        string $relatedRelationName = null
    ) {
        if ((!$presenter instanceof LinksContract && !$presenter instanceof RelatedLinksContract) || !$presenter->linksEnabled(
            )) {
            $this->links = collect([]);

            return;
        }

        $this->linkParameters = $linkParameters;

        if (!$related && $presenter instanceof LinksContract) {
            $this->setLinks($presenter);
        } elseif ($presenter instanceof RelatedLinksContract) {
            $this->setRelatedLinks($presenter, $relatedRelationName);
        }
    }

    /**
     * @param LinksContract $links
     *
     * @return Links
     */
    private function setLinks(LinksContract $links): Links
    {
        $this->links = $links->data($this->linkParameters);

        return $this;
    }

    /**
     * @param RelatedLinksContract $relatedLinks
     * @param string|null          $relatedRelationName
     *
     * @return Links
     * @throws InvalidArgumentException
     */
    private function setRelatedLinks(RelatedLinksContract $relatedLinks, ?string $relatedRelationName = null): Links
    {
        if (null !== $relatedRelationName && $this->hasRelatedLinks($relatedLinks, $relatedRelationName)) {
            $this->links = collect($relatedLinks->relatedData($this->linkParameters)->get($relatedRelationName));
        } else {
            $this->links = collect([]);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    private function links(): Collection
    {
        return $this->links;
    }

    /**
     * @return Collection
     */
    public function present(): Collection
    {
        return collect(
            [
                'links' => $this->links()->map(fn(string $value) => explode('?', $value)[0])->toArray(),
            ]
        );
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->links()->isEmpty();
    }

    /**
     * @param RelatedLinksContract $relatedLinks
     * @param string               $relatedRelationName
     *
     * @return bool
     */
    private function hasRelatedLinks(RelatedLinksContract $relatedLinks, string $relatedRelationName): bool
    {
        return $relatedLinks->relatedData($this->linkParameters)->has($relatedRelationName);
    }
}
