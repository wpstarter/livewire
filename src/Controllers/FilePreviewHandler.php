<?php

namespace Livewire\Controllers;

use Livewire\FileUploadConfiguration;

class FilePreviewHandler
{
    use CanPretendToBeAFile;

    public function handle($filename)
    {
        ws_abort_unless(ws_request()->hasValidSignature(), 401);

        return $this->pretendResponseIsFile(
            FileUploadConfiguration::storage()->path(FileUploadConfiguration::path($filename)),
            FileUploadConfiguration::mimeType($filename)
        );
    }
}
