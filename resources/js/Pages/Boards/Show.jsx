import MainLayout from '@/Layouts/MainLayout';
import { Link, router } from '@inertiajs/react';

export default function BoardShow({ board, submissions, isMember }) {
  const join = () => router.post(`/boards/${board.id}/join`);

  return (
    <MainLayout>
      <div className="flex justify-between items-start mb-6">
        <div>
          <h1 className="text-3xl font-bold text-blue-900">{board.name}</h1>
          <p className="text-gray-600 mt-1">{board.description}</p>
        </div>
        {isMember
          ? <Link href={`/boards/${board.id}/submit`}
              className="bg-blue-900 text-white px-4 py-2 rounded">Post Notice</Link>
          : <button onClick={join}
              className="bg-green-600 text-white px-4 py-2 rounded">Join Board</button>
        }
      </div>
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {submissions.filter(s => s.status === 'approved').map(s => (
          <div key={s.id} className="bg-white p-4 rounded-lg shadow border-t-4 border-blue-900">
            <span className="text-xs uppercase tracking-wide text-blue-600">{s.type}</span>
            <h3 className="font-bold text-lg mt-1">{s.title}</h3>
            {s.type === 'flyer' && <div className="mt-2 text-gray-700" dangerouslySetInnerHTML={{__html: s.content}} />}
            {s.file_path && <img src={`/storage/${s.file_path}`} className="mt-2 w-full rounded" alt={s.title} />}
            <p className="text-xs text-gray-400 mt-3">Expires: {new Date(s.expires_at).toLocaleDateString()}</p>
          </div>
        ))}
      </div>
    </MainLayout>
  );
}
