<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;

trait FileStorage
{
    /**
     * Upload file in storage folder.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function uploadFile(UploadedFile $file): string
    {
        // Define el directorio donde se guardarán los archivos
        $directory = 'invoices/evidences';

        // Genera un nombre de archivo único
        $fileName = time() . '.' . $file->getClientOriginalExtension();

        // Guarda el archivo en el disco de almacenamiento configurado y obtiene el "path" relativo
        $filePath = $file->storeAs($directory, $fileName, 'local');

        // Devuelve el "path" relativo al archivo guardado
        return $filePath;
    }

    /**
     * get file in storage folder.
     *
     * @param string $filePath
     * @return string
     */
    public function getFile(string $filePath): string
    {
        // Obtiene el "path" absoluto del archivo
        $path = storage_path('app/' . $filePath);

        // Devuelve el archivo
        return file_get_contents($path);
    }
}
