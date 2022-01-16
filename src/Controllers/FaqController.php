<?php
#App\Plugins\Cms\Faq\Controllers\FaqController.php
namespace App\Plugins\Cms\Faq\Controllers;

use App\Plugins\Cms\Faq\Models\FaqCategory;
use App\Plugins\Cms\Faq\Models\FaqContent;
use App\Http\Controllers\RootAdminController;
use App\Plugins\Cms\Faq\AppConfig;

class FaqController extends RootAdminController
{
    public $plugin;
    public function __construct()
    {
        parent::__construct();
        $this->plugin = new AppConfig;
    }

    /**
     * List category FAQ
     *
     * @return  [type]  [return description]
     */
    public function list() {
        $itemsList = (new FaqCategory)
            ->setPaginate()
            ->setLimit(sc_config('item_list'))
            ->getData();
        sc_check_view($this->plugin->pathPlugin.'::faq_list');
        return view(
            $this->plugin->pathPlugin.'::faq_list',
            array(
                'title' => trans($this->plugin->pathPlugin.'::Content.faq_category'),
                'itemsList' => $itemsList,
                'keyword' => '',
                'description' => '',
                'layout_page' => 'item_list',
            )
        );
    }

    /**
     * Category detail
     * @return [type] [description]
     */
    public function category($alias)
    {
        $categoryCurrently = (new FaqCategory)->getDetail($alias, 'alias');
            if ($categoryCurrently) { 
                $entries = (new FaqContent)
                    ->getContentToCategory($categoryCurrently->id)
                    ->setPaginate()
                    ->getData();
            return view(
                $this->plugin->pathPlugin.'::faq_category',
                array(
                    'title' => $categoryCurrently['title'],
                    'description' => $categoryCurrently['description'],
                    'keyword' => $categoryCurrently['keyword'],
                    'entries' => $entries,
                    'layout_page' => 'content_list',
                )
            );
        } else {
            return view('templates.' . sc_store('template') . '.notfound',
            array(
                    'title' => sc_language_render('front.data_not_found'),
                    'description' => '',
                    'keyword' => sc_store('keyword'),
                    'msg' => sc_language_render('front.data_not_found'),
                )
            );
        }

    }

    /**
     * Content detail
     *
     * @param   [string]  $alias  [$alias description]
     *
     * @return  [type]          [return description]
     */
    public function content($alias)
    {
        $entryCurrently = (new FaqContent)->getDetail($alias, 'alias');
        if ($entryCurrently) {
            $title = ($entryCurrently) ? $entryCurrently->question : sc_language_render('front.data_not_found');
            return view($this->plugin->pathPlugin.'::faq_entry_detail',
                array(
                    'title' => $title,
                    'entryCurrently' => $entryCurrently,
                    'description' => $entryCurrently['description'],
                    'keyword' => $entryCurrently['keyword'],
                    'og_image' => $entryCurrently->getImage(),
                    'layout_page' => 'content_detail',
                )
            );
        } else {
            return view('templates.' . sc_store('template') . '.notfound',
                array(
                    'title' => sc_language_render('front.data_not_found'),
                    'description' => '',
                    'keyword' => sc_store('keyword'),
                    'msg' => sc_language_render('admin.data_not_found_msg'),
                )
            );
        }

    }
}
