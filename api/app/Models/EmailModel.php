<?php
/**
 *
 * @author           sbhatnagar
 * @date             6/1/19
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Class EmailModel
 * @package App\Models
 */
class EmailModel extends Model
{
    /**
     * @var string
     */
    protected $table = 'email_status';


    /**
     * Get Data based on dates and status
     *
     * @param Carbon $date
     * @param null   $status
     *
     * @return mixed
     */
    public function getEmailsWithInWeek(Carbon $date, $status = null): object
    {
        if (is_null($status)) {
            return $this->whereDate('created_at', '<=', $date->format('Y-m-d H:i:s'))
                ->whereDate('created_at', '>=', $date->subWeek()->format('Y-m-d H:i:s'));
        }

        return $this->whereDate('created_at', '<=', $date->format('Y-m-d H:i:s'))
            ->whereDate('created_at', '>=', $date->subWeek()->format('Y-m-d H:i:s'))
            ->where('status', '=', $status);
    }

    /**
     * call this function via a scheduler to delete every week
     * @return void
     */
    public function deleteOneWeekRecord(): void
    {
        //remove data from the system
    }
}