<?php

namespace App\Http\Controllers\Core;

use App\Actions\GuaranteeLetter\CreateGuaranteeLetter;
use App\Actions\GuaranteeLetter\GenerateGLPDF;
use App\Http\Controllers\Controller;

class GLController extends Controller
{
    public function createForApplication($application, $budgetUpdate, CreateGuaranteeLetter $createGL)
    {
        return $createGL->execute($application, $budgetUpdate);
    }

    public function generatePDF($application, GenerateGLPDF $generatePDF)
    {
        return $generatePDF->execute($application);
    }
}
