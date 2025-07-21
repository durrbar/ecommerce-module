<?php

namespace Modules\Ecommerce\Http\Controllers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Exceptions\DurrbarException;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Ecommerce\Repositories\NotifyLogsRepository;
use Modules\Role\Enums\Permission;
use Modules\User\Repositories\UserRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotifyLogsController extends CoreController
{
    public $repository;

    public $userRepository;

    public function __construct(NotifyLogsRepository $repository, UserRepository $userRepository)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    /**
     * index
     *
     * @return Collection|NotifyLogs[]
     */
    public function index(Request $request)
    {
        try {
            $limit = $request->limit ? $request->limit : 10;

            return $this->fetchNotifyLogs($request)->paginate($limit)->withQueryString();
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG, $th->getMessage());
        }
    }

    /**
     * fetchNotifyLogs
     *
     * @return object
     */
    public function fetchNotifyLogs(Request $request)
    {
        $user = $request->user();
        $notify_log_query = $this->repository->with(['sender_user'])->where('receiver', '=', $user->id);

        if (isset($request->notify_type) && ! empty($request->notify_type)) {
            $notify_log_query = $notify_log_query->where('notify_type', '=', $request->notify_type);
        }

        return $notify_log_query;
    }

    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return object
     */
    public function show(Request $request, $id)
    {
        try {
            $request['id'] = $id;

            return $this->fetchNotifyLog($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(NOT_FOUND);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return object
     */
    public function fetchNotifyLog(Request $request)
    {
        try {
            $id = $request['id'];

            return $this->repository->where('id', '=', $id)->firstOrFail();
        } catch (Exception $th) {
            throw new HttpException(404, NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return JsonResponse
     */
    public function destroy($id, Request $request)
    {
        try {
            $request['id'] = $id;

            return $this->deleteNotifyLogs($request);
        } catch (DurrbarException $th) {
            throw new DurrbarException(SOMETHING_WENT_WRONG, $th->getMessage());
        }
    }

    public function deleteNotifyLogs(Request $request)
    {
        try {
            if ($request->user()->hasPermissionTo(Permission::SUPER_ADMIN)) {
                return $this->repository->findOrFail($request->id)->delete();
            }
        } catch (DurrbarException $th) {
            throw new DurrbarException(NOT_AUTHORIZED, $th->getMessage());
        }
    }

    /**
     * readNotifyLogs
     *
     * @return void
     */
    public function readNotifyLogs(Request $request)
    {
        try {
            $notify_log = $this->repository->findOrFail($request->id);
            $notify_log->is_read = true;
            $notify_log->save();

            return $notify_log;
        } catch (DurrbarException $th) {
            throw new DurrbarException(NOT_AUTHORIZED, $th->getMessage());
        }
    }

    /**
     * readAllNotifyLogs
     *
     * @return void
     */
    public function readAllNotifyLogs(Request $request)
    {
        try {
            if (isset($request->set_all_read)) {
                $notify_logs = $this->repository->where('notify_type', '=', $request->notify_type)->where('receiver', '=', $request->receiver)->get();

                foreach ($notify_logs as $notify_log) {
                    $notify_log->is_read = true;
                    $notify_log->save();
                }

                return $notify_logs;
            }
        } catch (DurrbarException $th) {
            throw new DurrbarException(NOT_AUTHORIZED, $th->getMessage());
        }
    }
}
