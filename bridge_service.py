import asyncio
import websockets
import json
import serial
import serial.tools.list_ports
import threading
import queue
import time

# Configuration for your UHF RFID Reader (ISO18000-6C)
SERIAL_PORT = 'COM5'  
BAUD_RATE = 9600    
TAG_QUEUE = queue.Queue()

def serial_reader_thread(port, baud):
    """
    Dedicated thread to handle the Serial (RS232) communication.
    Updated specifically for your reader's ASCII format: [STX]TAG[CR][LF][ETX]
    """
    try:
        ser = serial.Serial(port, baud, timeout=0.05)
        print(f"CONNECTED: {port} @ {baud} baud.")
        print("Waiting for tags... (Wave a tag over the reader)")
        
        buffer = ""
        while True:
            if ser.in_waiting > 0:
                # Read raw bytes and decode to string, ignoring errors for non-ASCII noise
                raw_data = ser.read(ser.in_waiting).decode('ascii', errors='ignore')
                buffer += raw_data
                
                # Check for either ETX (0x03) or standard Newline/Carriage Return
                # PuTTY often shows data line-by-line, which means \r or \n
                if '\n' in buffer or '\r' in buffer or '\x03' in buffer:
                    try:
                        # Logic to split by any common delimiter
                        # We'll treat characters like \x02 (STX) and \x03 (ETX) as delimiters too
                        parts = buffer.replace('\x02', '\n').replace('\x03', '\n').replace('\r', '\n').split('\n')
                        
                        # Keep the last part in the buffer if it's incomplete
                        buffer = parts.pop()
                        
                        for raw_id in parts:
                            # Extract only digits from the captured segment
                            id_str = "".join([c for c in raw_id if c.isdigit()])
                            
                            if len(id_str) >= 7:
                                current_time = time.time()
                                if not hasattr(serial_reader_thread, 'last_tags'):
                                    serial_reader_thread.last_tags = {}
                                
                                last_seen = serial_reader_thread.last_tags.get(id_str, 0)
                                if (current_time - last_seen) > 3.0:
                                    print(f"\n[!] VALID TAG: {id_str}")
                                    TAG_QUEUE.put(id_str)
                                    serial_reader_thread.last_tags[id_str] = current_time
                            elif len(id_str) > 0:
                                print(f" - Ignored noise/packet: {id_str}")
                    except Exception as e:
                        print(f"Parse error: {e}")
                        buffer = ""
            
            time.sleep(0.01)
            
    except serial.SerialException as e:
        print(f"\nSERIAL ERROR: {e}")
    except Exception as e:
        print(f"\nBRIDGE ERROR: {e}")

async def websocket_handler(websocket, path=None):
    print("Web UI connected to Bridge.")
    try:
        while True:
            try:
                tag_id = TAG_QUEUE.get_nowait()
                await websocket.send(json.dumps({
                    "tagId": tag_id,
                    "source": "hardware",
                    "timestamp": time.strftime("%H:%M:%S")
                }))
                print(f"Forwarded to Website: {tag_id}")
            except queue.Empty:
                pass
            await asyncio.sleep(0.1)
    except websockets.exceptions.ConnectionClosed:
        print("Web UI disconnected.")

async def main():
    print("--- SmartGate UHF RFID Hardware Bridge ---")
    ports = list(serial.tools.list_ports.comports())
    print("\nAvailable COM Ports:")
    for p in ports: print(f" - {p}")
    
    print(f"\nAttempting to listen on {SERIAL_PORT}...")
    threading.Thread(target=serial_reader_thread, args=(SERIAL_PORT, BAUD_RATE), daemon=True).start()
    
    async with websockets.serve(websocket_handler, "0.0.0.0", 8080):
        await asyncio.get_running_loop().create_future()

if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print("\nBridge Service Stopped.")
