<?php

namespace App\Infrastructure\Controller;

use App\UseCase\TransformTweetsOfHashtagIntoFormat;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController
{
    /**
     * @Route("/export/{hashtagSlug}/{formatToExport}", name="export_hashtag")
     */
    public function exportHashtag(
        string $hashtagSlug,
        string $formatToExport,
        TransformTweetsOfHashtagIntoFormat $formatter): Response
    {
        return $formatter->dispatch($formatToExport, $hashtagSlug);
    }

}
