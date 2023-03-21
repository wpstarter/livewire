<?php

namespace Tests\Browser\FileDownloads;

use WpStarter\Support\Facades\View;
use WpStarter\Support\Facades\Storage;
use Livewire\Component as BaseComponent;

class Component extends BaseComponent
{
    protected $listeners = [
        'download'
    ];

    public function download()
    {
        ws_config()->set('filesystems.disks.dusk-tmp', [
            'driver' => 'local',
            'root' => __DIR__,
        ]);

        return Storage::disk('dusk-tmp')->download('download-target.txt');
    }

    public function downloadWithContentTypeHeader($contentType = null)
    {
        ws_config()->set('filesystems.disks.dusk-tmp', [
            'driver' => 'local',
            'root' => __DIR__,
        ]);

        return Storage::disk('dusk-tmp')->download('download-target.txt', null, ['Content-Type' => $contentType]);
    }

    public function downloadAnUntitledFileWithContentTypeHeader($contentType = 'text/html')
    {
        ws_config()->set('filesystems.disks.dusk-tmp', [
            'driver' => 'local',
            'root' => __DIR__,
        ]);

        return Storage::disk('dusk-tmp')->download('download-target.txt', '', ['Content-Type' => $contentType]);
    }

    public function downloadFromResponse()
    {
        ws_config()->set('filesystems.disks.dusk-tmp', [
            'driver' => 'local',
            'root' => __DIR__,
        ]);

        return ws_response()->download(
            Storage::disk('dusk-tmp')->path('download-target2.txt')
        );
    }

    public function downloadFromResponseWithContentTypeHeader()
    {
        ws_config()->set('filesystems.disks.dusk-tmp', [
            'driver' => 'local',
            'root' => __DIR__,
        ]);

        return ws_response()->download(
            Storage::disk('dusk-tmp')->path('download-target2.txt'),
            'download-target2.txt',
            ['Content-Type' => 'text/csv']
        );
    }

    public function downloadQuotedContentDispositionFilename()
    {
        ws_config()->set('filesystems.disks.dusk-tmp', [
            'driver' => 'local',
            'root' => __DIR__,
        ]);

        return Storage::disk('dusk-tmp')->download('download & target.txt');
    }

    public function downloadQuotedContentDispositionFilenameFromResponse()
    {
        ws_config()->set('filesystems.disks.dusk-tmp', [
            'driver' => 'local',
            'root' => __DIR__,
        ]);

        return ws_response()->download(
            Storage::disk('dusk-tmp')->path('download & target2.txt')
        );
    }

    public function render()
    {
        return View::file(__DIR__.'/view.blade.php');
    }
}
