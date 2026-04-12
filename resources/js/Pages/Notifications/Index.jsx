import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { route } from 'ziggy-js';

function NotificationCard({ notification }) {
    const markAsRead = () => {
        router.post(route('notifications.read', notification.id));
    };

    const boardId = notification.data?.board_id;

    return (
        <div
            className={
                'rounded-lg border p-4 shadow-sm ' +
                (notification.read_at ? 'bg-white border-gray-200' : 'bg-blue-50 border-blue-200')
            }
        >
            <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h3 className="text-lg font-semibold text-gray-900">
                        {notification.data?.title ?? 'Notification'}
                    </h3>
                    <p className="mt-1 text-sm text-gray-700">
                        {notification.data?.message ?? 'No message available.'}
                    </p>
                    <p className="mt-2 text-xs text-gray-500">
                        {notification.created_at}
                    </p>
                </div>

                <div className="flex flex-wrap gap-2">
                    {boardId && (
                        <Link
                            href={route('boards.show', boardId)}
                            className="rounded bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700"
                        >
                            View Board
                        </Link>
                    )}

                    {!notification.read_at && (
                        <button
                            onClick={markAsRead}
                            className="rounded bg-gray-700 px-3 py-2 text-sm text-white hover:bg-gray-800"
                        >
                            Mark as Read
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
}

export default function Index({ notifications }) {
    const { auth } = usePage().props;

    const markAllAsRead = () => {
        router.post(route('notifications.readAll'));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Notifications
                </h2>
            }
        >
            <Head title="Notifications" />

            <div className="py-12">
                <div className="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <div className="flex items-center justify-between gap-4">
                            <div>
                                <h3 className="text-lg font-semibold text-gray-900">Your Notifications</h3>
                                <p className="mt-1 text-sm text-gray-600">
                                    Track invites, moderation updates, and board role changes.
                                </p>
                            </div>

                            {notifications.length > 0 && (
                                <button
                                    onClick={markAllAsRead}
                                    className="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                                >
                                    Mark All as Read
                                </button>
                            )}
                        </div>
                    </div>

                    {notifications.length === 0 ? (
                        <div className="overflow-hidden bg-white p-6 text-gray-600 shadow-sm sm:rounded-lg">
                            You have no notifications yet.
                        </div>
                    ) : (
                        <div className="space-y-4">
                            {notifications.map((notification) => (
                                <NotificationCard
                                    key={notification.id}
                                    notification={notification}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}