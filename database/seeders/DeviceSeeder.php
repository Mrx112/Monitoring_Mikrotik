use Illuminate\Database\Seeder;
use App\Models\Device;

class DeviceSeeder extends Seeder
{
    public function run()
    {
        Device::create([
            'name' => 'Mikrotik Simpang Lima',
            'ip_address' => '192.168.88.1',
            'latitude' => -7.005145,
            'longitude' => 110.438125
        ]);
        Device::create([
            'name' => 'Mikrotik Lawang Sewu',
            'ip_address' => '192.168.88.2',
            'latitude' => -7.001478,
            'longitude' => 110.415609
        ]);
    }
}
