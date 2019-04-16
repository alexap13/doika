<?php

namespace Diglabby\Doika\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Diglabby\Doika\Models\Campaign as Campaign;
use Illuminate\Http\Request;

final class CampaignController extends Controller
{
    /**
     * @method index
     *
     * Returns collection of campaigns in json format. Count of campaigns defined by perPage parameter from $request
     *
     * @param Request $request
     * json format
     * {
     *  perPage: {int}
     * }
     *
     * @return response (json, status)
     * json records format
     * {
     *  id: {int}
     *  name: {string}
     *  active_status: {bool}
     *  count_of_donators: {int} -> count of donators with (donator.campaign_id == this.id) from table donators
     *  is_recurrent: {bool}
     *  average_donate_amount: {int} -> this.overall_donate_amount / count of all successful transaction with transaction.campaign_id == this.id
     *  overall_donate_amount: {int} -> summ of all transaction.amount with (transaction.status == 'successful') and (transaction.campaign_id == this.id) from table transactions
     *  target_amount: {int}
     *  start_at: {time}
     *  finish_at: {time}
     * }
     *
     */
    public function index(Request $request)
    {

        try {

            $countPerPage = $request->perPage;
            $campaignsCollection = Campaign::all('id', 'name', 'active_status')->paginate($countPerPage);

            if(!$campaignsCollection)
                $status = 404;
            else
                $status = 200;

        } catch (exception $e) {

            $status = 500;

        }

        return response( $campaignsCollection, $status);
    }


    /**
     * @method show
     *
     * Returns data of specific campaign in json format. Campaign defined by campaignId parameter from $request
     *
     * @param Request $request
     *
     * {
     *  campaignId: {int}
     * }
     *
     * @return response (json, status)
     * json format
     * {
     *  id: {int}
     *  name: {string}
     *  description: {string}
     *  picture_url: {string}
     *  active_status: {bool}
     *  start_at: {timestamp}
     *  finish_at: {timestamp}
     *  target_amount: {int}
     *  is_recurrent: {bool}
     * }
     */
    public function show($campaignId)
    {
        return response(factory(Campaign::class)->make(['id' => $campaignId]), 200);
    }

    /**
     * @method store
     *
     * Creates new campaign.
     *
     * @param Request $request
     * json format
     * {
     *  name: {string}
     *  description: {string}
     *  picture_url: {string}
     *  active_status: {bool}
     *  start_at: {timestamp}
     *  finish_at: {timestamp}
     *  target_amount: {int}
     *  is_recurrent: {bool}
     * }
     *
     * @return response (json, status)
     * json format
     * {
     *  id: {int}
     * }
     */
    public function store(Request $request)
    {
        $campaign = new Campaign($request->all());
        $campaign->save();

        return response($campaign, 200);
    }


    /**
     * @method update
     *
     * Updates data of specific campaign. Campaign defined by campaignId parameter from $request
     *
     * @param Request $request
     *
     * json format
     * {
     *  campaignId: {int}
     *  [] => {
     *      name: {string}
     *      description: {string}
     *      picture_url: {string}
     *      active_status: {bool}
     *      start_at: {timestamp}
     *      finish_at: {timestamp}
     *      target_amount: {int}
     *      is_recurrent: {bool}
     *   }
     * }
     *
     * @return response (json, status)
     * json format
     * {
     *  id: {int}
     * }
     */
    public function update(Request $request)
    {
        $campaign = Campaign::query()->findOrFail($request->campaignId);
        if($campaign) {


            $campaign->name = $request->campaign->name;

            $campaign->save();
            return response('success', 200);
        }

        return response(null,500);
    }

    /**
     * @method delete
     *
     * Remove Campaign from table. Campaign defined by campaignId parameter from $request
     *
     * @param Request $request
     *  json format
     * {
     *  campaignId: {int}
     * }
     *
     * @return response (json, status)
     *
     */
    public function delete($campaignId)
    {
        try {

            $campaignsRemoved = Campaign::where('id', $campaignId)->delete();
            if(!$campaignsRemoved)
                $status = 404;
            else
                $status = 200;

        } catch (exception $e) {

            $status = 500;

        }

        return response( $campaignsRemoved, $status);

    }

    /**
     * @method activeToggle
     *
     * Enable/disable campaign
     *
     * @param Request $request
     *
     * {
     *  campaignId: {int}
     * }
     *
     * @return response (json, status)
     *
     */
    public function activeToggle(Request $request)
    {
        $campaign = Campaign::query()->findOrFail($request->campaignId);
        $campaign->update(['active_status' => ! $campaign->active_staus]);
        return response( null, 200);
    }
}
