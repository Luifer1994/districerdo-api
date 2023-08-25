<?php

namespace App\Http\Modules\DocumentTypes\Repositories;

use App\Http\Modules\Bases\RepositoryBase;
use App\Http\Modules\DocumentTypes\Models\DocumentType;

class DocumentTypeRepository extends RepositoryBase
{
    protected  $DocumentTypeModel;

    public function __construct(DocumentType $DocumentTypeModel)
    {
        parent::__construct($DocumentTypeModel);
        $this->DocumentTypeModel = $DocumentTypeModel;
    }

    /**
     * Get all document types.
     *
     * @return object
     * @author Luifer Almendrales
     */
    public function getAllDocumentTypes(): object
    {
        return $this->DocumentTypeModel
            ->select('id', 'name', 'code')
            ->get();
    }
}
