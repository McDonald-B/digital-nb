import MainLayout from '@/Layouts/MainLayout';
import { router } from '@inertiajs/react';

export default function AdminIndex({ pendingSubmissions }) {
  const approve = (id) => router.patch(`/admin/submissions/${id}/approve`);
  const reject  = (id) => router.patch(`/admin/submissions/${id}/reject`);
  const remove  = (id) => router.delete(`/admin/submissions/${id}`);

  return (
    <MainLayout>
      <h1 className="text-3xl font-bold text-blue-900 mb-6">Admin Dashboard</h1>
      <p className="mb-4 text-gray-600">{pendingSubmissions.length} submission(s) awaiting review</p>
      <div className="space-y-4">
        {pendingSubmissions.map(s => (
          <div key={s.id} className="bg-white p-4 rounded-lg shadow flex justify-between items-center">
            <div>
              <span className="text-xs uppercase text-blue-600">{s.type}</span>
                <h3 className="font-semibold">{s.title}</h3>
              <p className="text-sm text-gray-500">Board: {s.board.name} | User: {s.user.name}</p>
            </div>
            <div className="flex gap-2">
              <button onClick={() => approve(s.id)}
                className="bg-green-600 text-white px-3 py-1 rounded text-sm">Approve</button>
              <button onClick={() => reject(s.id)}
                className="bg-yellow-500 text-white px-3 py-1 rounded text-sm">Reject</button>
              <button onClick={() => remove(s.id)}
                className="bg-red-600 text-white px-3 py-1 rounded text-sm">Delete</button>
            </div>
          </div>
        ))}
      </div>
    </MainLayout>
  );
}
