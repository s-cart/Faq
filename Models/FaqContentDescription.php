<?php
#app/Plugins/Cms/Faq/Models/FaqContentDescription.php
namespace App\Plugins\Cms\Faq\Models;

use Illuminate\Database\Eloquent\Model;

class FaqContentDescription extends Model
{
    protected $primaryKey = ['lang', 'content_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = SC_DB_PREFIX.'faq_content_description';
    protected $connection = SC_CONNECTION;
}
