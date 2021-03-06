<?php

namespace AppBundle\Api;

use AppBundle\Repository\LegislativeCandidateRepository;
use AppBundle\Twig\AssetExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LegislativeCandidateProvider
{
    private $repository;
    private $asset;
    private $urlGenerator;

    public function __construct(LegislativeCandidateRepository $repository, AssetExtension $asset, UrlGeneratorInterface $urlGenerator)
    {
        $this->repository = $repository;
        $this->asset = $asset;
        $this->urlGenerator = $urlGenerator;
    }

    public function getForApi(): array
    {
        foreach ($this->repository->findAllForDirectory() as $candidate) {
            if (!$candidate->getLatitude() || !$candidate->getLongitude()) {
                continue;
            }

            $data[] = [
                'id' => $candidate->getId(),
                'name' => $candidate->getFullName(),
                'district' => $candidate->getDistrictName(),
                'picture' => $candidate->getMedia() ? $this->asset->transformedMediaAsset($candidate->getMedia(), ['w' => 200, 'h' => 140, 'q' => 90, 'fit' => 'crop']) : '',
                'url' => $this->urlGenerator->generate('legislatives_candidate', ['slug' => $candidate->getSlug()]),
                'position' => [
                    'lat' => $candidate->getLatitude(),
                    'lng' => $candidate->getLongitude(),
                ],
            ];
        }

        return $data ?? [];
    }
}
