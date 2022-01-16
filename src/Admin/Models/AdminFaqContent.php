<?php

namespace App\Plugins\Cms\Faq\Admin\Models;

use App\Plugins\Cms\Faq\Models\FaqContent;
use App\Plugins\Cms\Faq\Models\FaqContentDescription;
use Cache;

class AdminFaqContent extends FaqContent
{
    protected static $getListTitleAdmin = null;
    protected static $getListContentGroupByParentAdmin = null;
    /**
     * Get content detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getContentAdmin($id) {
        return self::where('id', $id)
        ->where('store_id', session('adminStoreId'))
        ->first();
    }

    /**
     * Get list content in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public function getContentListAdmin(array $dataSearch) {
        $keyword          = $dataSearch['keyword'] ?? '';
        $sort_order       = $dataSearch['sort_order'] ?? '';
        $arrSort          = $dataSearch['arrSort'] ?? '';
        $tableDescription = (new FaqContentDescription)->getTable();
        $tableContent    = $this->getTable();

        $contentList = (new FaqContent)
            ->leftJoin($tableDescription, $tableDescription . '.content_id', $tableContent . '.id')
            ->where('store_id', session('adminStoreId'))
            ->where($tableDescription . '.lang', sc_get_locale());

        if ($keyword) {
            $contentList = $contentList->where(function ($sql) use($tableDescription, $keyword){
                $sql->where($tableDescription . '.question', 'like', '%' . $keyword . '%');
            });
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $contentList = $contentList->sort($field, $sort_field);
        } else {
            $contentList = $contentList->sort('id', 'desc');
        }
        $contentList = $contentList->paginate(20);

        return $contentList;
    }

    /**
     * Get array title content
     * user for admin 
     *
     * @return  [type]  [return description]
     */
    public static function getListTitleAdmin()
    {
        $tableDescription = (new FaqContentDescription)->getTable();
        $table = (new AdminFaqContent)->getTable();
        if (sc_config_global('cache_status') && sc_config_global('cache_content')) {
            if (!Cache::has(session('adminStoreId').'_cache_content_'.sc_get_locale())) {
                if (self::$getListTitleAdmin === null) {
                    self::$getListTitleAdmin = self::join($tableDescription, $tableDescription.'.content_id', $table.'.id')
                    ->where('lang', sc_get_locale())
                    ->where('store_id', session('adminStoreId'))
                    ->pluck('question', 'id')
                    ->toArray();
                }
                sc_set_cache(session('adminStoreId').'_cache_content_'.sc_get_locale(), self::$getListTitleAdmin);
            }
            return Cache::get(session('adminStoreId').'_cache_content_'.sc_get_locale());
        } else {
            if (self::$getListTitleAdmin === null) {
                self::$getListTitleAdmin = self::join($tableDescription, $tableDescription.'.content_id', $table.'.id')
                ->where('lang', sc_get_locale())
                ->where('store_id', session('adminStoreId'))
                ->pluck('question', 'id')
                ->toArray();
            }
            return self::$getListTitleAdmin;
        }
    }

    /**
     * Create a new content
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function createContentAdmin(array $dataInsert) {

        return self::create($dataInsert);
    }


    /**
     * Insert data description
     *
     * @param   array  $dataInsert  [$dataInsert description]
     *
     * @return  [type]              [return description]
     */
    public static function insertDescriptionAdmin(array $dataInsert) {

        return FaqContentDescription::create($dataInsert);
    }

    /**
     * [checkAliasValidationAdmin description]
     *
     * @param   [type]$type     [$type description]
     * @param   null  $fieldValue    [$field description]
     * @param   null  $categoryId      [$categoryId description]
     * @param   null  $storeId  [$storeId description]
     * @param   null            [ description]
     *
     * @return  [type]          [return description]
     */
    public function checkAliasValidationAdmin($type = null, $fieldValue = null, $categoryId = null, $storeId = null) {
        $storeId = $storeId ? $storeId : session('adminStoreId');
        $type = $type ? $type : 'alias';
        $fieldValue = $fieldValue;
        $categoryId = $categoryId;
        $tablePTS = (new AdminFaqContent)->getTable();
        $check =  $this
        ->where($type, $fieldValue)
        ->where($tablePTS . '.store_id', $storeId);
        if($categoryId) {
            $check = $check->where('id', '<>', $categoryId);
        }
        $check = $check->first();

        if($check) {
            return false;
        } else {
            return true;
        }
    }

}
