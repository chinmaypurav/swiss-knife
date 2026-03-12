<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('s3');
});

it('displays the file manager page', function () {
    $this->get(route('s3.index'))
        ->assertSuccessful()
        ->assertViewIs('s3.file-manager');
});

it('lists files from S3', function () {
    Storage::disk('s3')->put('document.pdf', 'content');
    Storage::disk('s3')->put('image.png', 'content');

    $this->get(route('s3.index'))
        ->assertSuccessful()
        ->assertSee('document.pdf')
        ->assertSee('image.png');
});

it('lists files in a subdirectory', function () {
    Storage::disk('s3')->put('photos/vacation.jpg', 'content');

    $this->get(route('s3.index', ['path' => 'photos']))
        ->assertSuccessful()
        ->assertSee('vacation.jpg');
});

it('lists directories', function () {
    Storage::disk('s3')->put('documents/file.txt', 'content');

    $this->get(route('s3.index'))
        ->assertSuccessful()
        ->assertSee('documents');
});

it('uploads a file to S3', function () {
    $file = UploadedFile::fake()->create('report.pdf', 100);

    $this->post(route('s3.upload'), [
        'file' => $file,
        'path' => '',
    ])->assertRedirect();

    Storage::disk('s3')->assertExists('report.pdf');
});

it('uploads a file to a subdirectory', function () {
    $file = UploadedFile::fake()->create('photo.jpg', 200);

    $this->post(route('s3.upload'), [
        'file' => $file,
        'path' => 'images',
    ])->assertRedirect();

    Storage::disk('s3')->assertExists('images/photo.jpg');
});

it('validates upload requires a file', function () {
    $this->post(route('s3.upload'), [])
        ->assertSessionHasErrors('file');
});

it('downloads a file from S3', function () {
    Storage::disk('s3')->put('test.txt', 'hello world');

    $this->get(route('s3.download', ['path' => 'test.txt']))
        ->assertSuccessful()
        ->assertDownload('test.txt');
});

it('returns 404 when downloading a non-existent file', function () {
    $this->get(route('s3.download', ['path' => 'missing.txt']))
        ->assertNotFound();
});

it('validates download requires a path', function () {
    $this->get(route('s3.download'))
        ->assertInvalid('path');
});

it('deletes a file from S3', function () {
    Storage::disk('s3')->put('delete-me.txt', 'content');

    $this->delete(route('s3.destroy', ['path' => 'delete-me.txt']))
        ->assertRedirect();

    Storage::disk('s3')->assertMissing('delete-me.txt');
});

it('returns 404 when deleting a non-existent file', function () {
    $this->delete(route('s3.destroy', ['path' => 'ghost.txt']))
        ->assertNotFound();
});

it('generates a signed URL', function () {
    Storage::fake('s3');

    // temporaryUrl is not supported by the fake disk, so we mock it
    Storage::shouldReceive('disk')
        ->with('s3')
        ->andReturn($mock = Mockery::mock());

    $mock->shouldReceive('exists')
        ->with('file.pdf')
        ->andReturn(true);

    $mock->shouldReceive('temporaryUrl')
        ->once()
        ->andReturn('https://s3.example.com/signed-url');

    $this->postJson(route('s3.signed-url'), [
        'path' => 'file.pdf',
        'expiry' => 30,
    ])
        ->assertSuccessful()
        ->assertJson(['url' => 'https://s3.example.com/signed-url']);
});

it('validates signed URL requires a path', function () {
    $this->postJson(route('s3.signed-url'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('path');
});
