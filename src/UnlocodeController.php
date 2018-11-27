<?php

namespace Dc\Unlocodes;

use Dc\Unlocodes\Helpers\UnlocodeHelper;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UnlocodeController extends Controller
{
    use ValidatesRequests;

    public $APIColumns = ['name','countrycode','subdivision','placecode','longitude','latitude','status','date','IATA'];

    /**
     * Display a listing of UNLOCodes.
     * GET    /unlocodes    index    unlocode.index
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Unlocode::paginate());
    }

    /**
     * Display the specified UNLOCode.
     * GET    /unlocodes/{unlocode}    show    unlocode.show
     *
     * @param  Unlocode $unlocode The unlocode object, resolved by route/model binding
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Unlocode $unlocode)
    {
        return response()->json($unlocode);
    }

    /**
     * Display the specified UNLOCode.
     * GET    /unlocodes/search/{term}    search    unlocodes.search
     *
     * @param  string $term
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(string $term)
    {
        $value = \Cache::remember(
            "unlocode_search_{$term}",
            5,
            function () use ($term) {
                $likeTerm = "%{$term}%";
                return Unlocode::where('name', 'LIKE', $likeTerm)
                    ->orWhere('countrycode', 'LIKE', $likeTerm)
                    ->orWhere('placecode', 'LIKE', $likeTerm)
                    ->get($this->APIColumns);
            }
        );
        return $value;
    }

    /**
     * Store a newly created UNLOCode in storage.
     * POST    /unlocode    store    unlocode.store
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, Unlocode::VALIDATION_RULES);

        try {
            // Create the unlocode
            $result = Unlocode::create(
                array_merge(
                    request(
                        [
                        'countrycode',
                        'placecode',
                        'subdivision',
                        'name',
                        'longitude',
                        'latitude']
                    ),
                    ['date' => date('ym')]
                )
            );

            return response()->json($result, 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            $code = request(['countrycode', 'placecode']);
            return response()->json([
                'message' => 'Failed to create unlocode ' . implode('', $code),
                'debug' => \App::environment(['production']) ? $e->getCode() : $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH    /unlocode/{unlocode}    update    unlocode.update
     *
     * @param  Unlocode $unlocode The unlocode object, resolved by route/model binding
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Unlocode $unlocode, Request $request)
    {
        $this->validate($request, Unlocode::VALIDATION_RULES);

        try {
            $success =
                $unlocode->update(
                    request(
                        [
                        'countrycode',
                        'placecode',
                        'subdivision',
                        'name',
                        'longitude',
                        'latitude'
                        ]
                    )
                );
            return response()->json($success, 204);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            $code = request(['countrycode', 'placecode']);
            return response()->json([
                'message' => 'Failed to update unlocode ' . implode('', $code),
                'debug' => \App::environment(['production']) ? $e->getCode() : $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE    /unlocode/{unlocode}    destroy    unlocode.destroy
     *
     * @param  Unlocode $unlocode The unlocode object, resolved by route/model binding
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception If primary key was somehow not defined for the model during delete
     */
    public function destroy(Unlocode $unlocode)
    {
        \Cache::forget(UnlocodeHelper::cacheKey($unlocode->countrycode, $unlocode->placecode));
        $success = $unlocode->delete() === true;
        return \Response::json($success);
    }
}
