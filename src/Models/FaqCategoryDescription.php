<?php
#app/Plugins/Cms/Models/Faq/FaqCategoryDescription.php
namespace App\Plugins\Cms\Faq\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FaqCategoryDescription extends Model
{
    protected $primaryKey = ['lang', 'category_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = SC_DB_PREFIX.'faq_category_description';
    protected $connection = SC_CONNECTION;
}
