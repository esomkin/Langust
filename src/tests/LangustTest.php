<?php

use App\Models\Article;

class LangustTest extends TestCase 
{

	private $article = null;

	public function setUp()
	{
		parent::setUp();

		$this->prepareForTests();
	}

	public function prepareForTests()
	{
		$article = App\Models\Article::where('url', '=', 'test')->first();
		if (!$article) {

			$article = App\Models\Article::create([

				'url' 	=> 'test',
				'en' 	=> [

					'name' 	=> 'Test article name en',
					'title' => 'Test article title en',
				],
			]);
		}

		$this->article = $article;
	}

    public function testArticleCreation()
    {
		$this->assertEquals($this->article->url, 'test');
    }

    public function testArticleGetTranslationViaTranslate()
    {
    	$this->assertEquals($this->article->translate('en')->name, 'Test article name en');
    }

    public function testArticleGetTranslationViaAttribute()
    {
    	$this->assertEquals($this->article->en->name, 'Test article name en');
    }

    public function testArticleGetTranslationViaLocale()
    {
    	$this->assertEquals($this->article->name, 'Test article name en');
    }

}
