<?php
#app/Plugins/Cms/FaqContent/Models/FaqContent.php
namespace App\Plugins\Cms\Faq\Models;

use App\Plugins\Cms\Faq\Models\FaqCategory;
use App\Plugins\Cms\Faq\Models\FaqContentDescription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use SCart\Core\Front\Models\ModelTrait;

class FaqContent extends Model
{
    use ModelTrait;

    public $table = SC_DB_PREFIX.'faq_content';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    protected  $sc_category = []; 

    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'category_id', 'id');
    }

    public function descriptions()
    {
        return $this->hasMany(FaqContentDescription::class, 'content_id', 'id');
    }
    public function images()
    {
        return $this->hasMany(FaqImage::class, 'content_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($content) {
            //Delete content descrition
            $content->descriptions()->delete();
        });
    }


    /*
    Get thumb
    */
    public function getThumb()
    {
        return sc_image_get_path_thumb($this->image);
    }

    /*
    Get image
    */
    public function getImage()
    {
        return sc_image_get_path($this->image);

    }

    /**
     * [getUrl description]
     * @return [type] [description]
     */
    public function getUrl()
    {
        return sc_route('faq.content', ['alias' => $this->alias]);
    }

    //Scort
    public function scopeSort($query, $column = null)
    {
        $column = $column ?? 'sort';
        return $query->orderBy($column, 'asc')->orderBy('id', 'desc');
    }

    /**
     * Get news detail
     *
     * @param   [string]  $key     [$key description]
     * @param   [string]  $type  [id, alias]
     *
     */
    public function getDetail($key, $type = null)
    {
        if(empty($key)) {
            return null;
        }
        $tableDescription = (new FaqContentDescription)->getTable();

        $content = $this
            ->leftJoin($tableDescription, $tableDescription . '.content_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', sc_get_locale());

        if ($type == null) {
            $content = $content->where('id', (int) $key);
        } else {
            $content = $content->where($type, $key);
        }
        $content = $content->where('status', 1)
            ->where('store_id', config('app.storeId'));
        return $content->first();
    }

    /**
     * Get list faq content
     *
     * @param   array  $arrOpt
     * Example: ['status' => 1, 'top' => 1]
     * @param   array  $arrSort
     * Example: ['sortBy' => 'id', 'sortOrder' => 'asc']
     * @param   array  $arrLimit  [$arrLimit description]
     * Example: ['step' => 0, 'limit' => 20]
     * @return  [type]             [return description]
     */
    public function getList($arrOpt = [], $arrSort = [], $arrLimit = [])
    {
        $sortBy = $arrSort['sortBy'] ?? null;
        $sortOrder = $arrSort['sortOrder'] ?? 'asc';
        $step = $arrLimit['step'] ?? 0;
        $limit = $arrLimit['limit'] ?? 0;

        $data = $this->sort($sortBy, $sortOrder);
        if(count($arrOpt = [])) {
            foreach ($arrOpt as $key => $value) {
                $data = $data->where($key, $value);
            }
        }
        if((int)$limit) {
            $start = $step * $limit;
            $data = $data->offset((int)$start)->limit((int)$limit);
        }
        $data = $data->get()->groupBy('id');

        return $data;
    }

    //=========================

    public function uninstall()
    {
        if (Schema::hasTable($this->table)) {
            Schema::drop($this->table);
        }
        if (Schema::hasTable($this->table.'_description')) {
            Schema::drop($this->table.'_description');
        }
    }

    public function install()
    {
        $this->uninstall();

        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->default(0);
            $table->string('image', 100)->nullable();
            $table->string('alias', 120)->index();
            $table->tinyInteger('sort')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('store_id')->default(1)->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create($this->table.'_description', function (Blueprint $table) {
            $table->integer('content_id');
            $table->string('lang', 10);
            $table->string('question', 200)->nullable();
            $table->string('keyword', 200)->nullable();
            $table->string('description', 300)->nullable();
            $table->text('answer')->nullable();
            $table->primary(['content_id', 'lang']);
        });

        DB::connection(SC_CONNECTION)->table($this->table)->insert(
            [
                ['id' => 1, 'alias' =>  'is-s-cart-free', 'image' => '/data/cms-image/cms_content_1.jpg', 'category_id' => 1,  'sort' => 0, 'status' => '1', 'created_at' => date("Y-m-d"), 'store_id' => 1],                    
                ['id' => 2, 'alias' =>  'does-s-cart-only-support-shop', 'image' => '/data/cms-image/cms_content_2.jpg', 'category_id' => 1,  'sort' => 0, 'status' => '1', 'created_at' => date("Y-m-d"), 'store_id' => 1],                    
            ]
        );

        DB::connection(SC_CONNECTION)->table($this->table.'_description')->insert(
            [
                [
                    'content_id' => '1', 'lang' => 'en', 'keyword' => '', 'description' => '', 
                    'question' => 'Is S-Cart free?',
                    'answer' => 'Yes, S-Cart is free and open source for everyone.'
                ],
                [
                    'content_id' => '1', 'lang' => 'vi', 'keyword' => '', 'description' => '', 
                    'question' => 'S-Cart có miễn phí không?',
                    'answer' => 'Vâng, S-Cart là mã nguồn mở miễn phí cho tất cả mọi người.'
                ],

                [
                    'content_id' => '2', 'lang' => 'en', 'keyword' => '', 'description' => '', 
                    'question' => 'Does S-Cart only support shop building function?',
                    'answer' => 'No. We support e-commerce platforms for individuals and businesses. However, our goal will be greater than that. S-Cart will be a major ecosystem in the future.'
                ],

                [
                    'content_id' => '2', 'lang' => 'vi', 'keyword' => '', 'description' => '', 
                    'question' => 'Có phải S-Cart chỉ hỗ trợ chức năng xây dựng cửa hàng?',
                    'answer' => 'Không. Chúng tôi hỗ trợ nền tảng thương mại điện tử cho cá nhân, doanh nghiệp.  Tuy nhiên, mục tiêu của chúng tôi sẽ lớn hơn thế. S-Cart sẽ là một hệ sinh thái lớn trong tương lai.'
                ],

                
            ]
        );


    }


    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start() {
        return new FaqContent;
    }
    
    /**
     * Set array category 
     *
     * @param   [array|int]  $category 
     *
     */
    private function setCategory($category) {
        if (is_array($category)) {
            $this->sc_category = $category;
        } else {
            $this->sc_category = array((int)$category);
        }
        return $this;
    }

    /**
     * Get content to array Catgory
     * @param   [array|int]  $arrCategory 
     */
    public function getContentToCategory($arrCategory) {
        $this->setCategory($arrCategory);
        return $this;
    }

    /**
     * build Query
     */
    public function buildQuery() {
        $tableDescription = (new FaqContentDescription)->getTable();

        //description
        $query = $this
            ->leftJoin($tableDescription, $tableDescription . '.content_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', sc_get_locale());
        //search keyword
        if ($this->sc_keyword !='') {
            $query = $query->where(function ($sql) use($tableDescription){
                $sql->where($tableDescription . '.question', 'like', '%' . $this->sc_keyword . '%');
            });
        }

        if (count($this->sc_category)) {
            $query = $query->whereIn('category_id', $this->sc_category);
        }

        $query = $query->where('status', 1)
            ->where('store_id', config('app.storeId'));

        if (count($this->sc_moreWhere)) {
            foreach ($this->sc_moreWhere as $key => $where) {
                if(count($where)) {
                    $query = $query->where($where[0], $where[1], $where[2]);
                }
            }
        }
        if ($this->sc_random) {
            $query = $query->inRandomOrder();
        }

        return $query;
    }    

}
