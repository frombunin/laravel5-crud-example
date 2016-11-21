<?php

namespace App\Http\Services;

use \Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class PageService
{
    protected $pages;

    public static $saveRules = [
        'name' => 'required|min:1|max:256'
    ];

    public function __construct(\App\Page $pages)
    {
        $this->pages = $pages;
    }

    public function find($id)
    {
        return $this->pages->find($id);
    }

    public function save(array $attrs)
    {
        $validator = Validator::make($attrs, self::$saveRules);
    		if ($validator->fails()) {
          return $validator;
        }

        $attrs['slug'] = $this->generateSlug($attrs['name']);

        if ( isset($attrs['id']) && $page = $this->pages->find($attrs['id']) ) {
            $page->update($attrs);
            return $page;
        }
        return $this->create($attrs);
    }

    public function create($attrs)
    {
      return $this->pages->create($attrs);
    }

    public function update($attrs)
    {
        return $this->pages->update($attrs);
    }

    public function take()
    {
        return $this->pages->orderBy('created_at', -1)->get();
    }

    public function delete($id)
    {
        $page = $this->pages->findOrFail($id);
        return $page->delete();
    }

    public function findWithLinksAndPictures($id)
    {
        return $this->pages->with('links')->with('pictures')->findOrFail($id);
    }

    public function createLink(array $attrs)
    {
        $page = $this->pages->findOrFail($attrs['page_id']);
        return $page->links()->create($attrs);
    }

    public function createPicture(array $attrs)
    {
      $page = $this->pages->findOrFail($attrs['page_id']);
      return $page->pictures()->create($attrs);
    }

    public function links($id)
    {
        return $this->pages->findOrFail($id)->links()->orderBy('created_at', -1)->get();
    }

    public function pictures($id)
    {
        return $this->pages->findOrFail($id)->pictures()->orderBy('created_at', -1)->get();
    }

    protected function generateSlug($name)
    {
        $array = explode(" ", $name);
        $result = implode("-", $array);
        return $result.'.html';
    }

}
